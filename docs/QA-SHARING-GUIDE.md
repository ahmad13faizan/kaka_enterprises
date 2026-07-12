# QA Sharing Guide — Expose Local Bagisto to the Internet (Free)

Share your laptop's running Bagisto with QA / testers over the public
internet. No VPS, no cost, real HTTPS. Uses **Cloudflare Tunnel**.

---

## One-Command Share

```bash
./share-qa.sh
```

This starts Docker services + the PHP server + the tunnel, then prints a
public URL like:

```
https://random-words-here.trycloudflare.com
```

Share that URL with QA. They can open it from anywhere — phone, another
city, anywhere with internet. Admin panel is at `<url>/admin`.

Press **Ctrl+C** to stop sharing (the public URL dies instantly).

---

## How It Works

```
   QA Tester's Browser
          │
          ▼
   https://xxxx.trycloudflare.com   ← public HTTPS URL (Cloudflare)
          │
          ▼  (encrypted tunnel)
   cloudflared (running on your laptop)
          │
          ▼
   http://localhost:8001            ← your Bagisto (php artisan serve)
          │
          ▼
   MySQL + Redis + Mailpit (Docker)
```

- Cloudflare gives you a temporary public HTTPS subdomain.
- The tunnel forwards traffic to your local server.
- Laravel trusts the proxy headers (`trustProxies('*')` is already set),
  so it auto-builds correct URLs for the tunnel domain — assets, forms,
  and links all work.

---

## Important Notes

### The URL changes each time

Every time you run `./share-qa.sh`, you get a NEW random URL. Share the
fresh one. (If you need a permanent URL, see "Stable URL" below.)

### Your laptop must stay on

The site is live only while your laptop is on, the script is running, and
you have internet. Close the laptop or stop the script → site goes down.
This is fine for QA sessions, not for real production.

### Emails go to Mailpit, not real inboxes

Verification emails, order confirmations, etc. land in Mailpit at
`http://localhost:8025` (on YOUR laptop). QA testers won't receive real
emails. If QA needs to test email flows:

- You watch Mailpit and relay the verification links, OR
- Switch to a real SMTP provider in `.env` (Gmail SMTP / Brevo free tier).

### Performance

Runs on your laptop, so speed depends on your machine + internet upload.
Fine for a handful of QA testers clicking through. Not for load testing.

---

## Stable URL (Optional — needs free Cloudflare account)

The `trycloudflare.com` URL is random/temporary. For a fixed URL that
doesn't change between restarts, use a named tunnel with a free Cloudflare
account + a domain:

1. Sign up free at https://dash.cloudflare.com
2. `cloudflared tunnel login`
3. `cloudflared tunnel create bagisto-qa`
4. Map it to a subdomain you control and run
   `cloudflared tunnel run bagisto-qa`

(Only worth it if QA testing is ongoing. For one-off sessions, the random
URL is simpler.)

---

## Alternative: ngrok

If you prefer ngrok (free tier gives 1 static domain):

```bash
# Install
curl -s https://ngrok-agent.s3.amazonaws.com/ngrok.asc | sudo tee /etc/apt/trusted.gpg.d/ngrok.asc >/dev/null
echo "deb https://ngrok-agent.s3.amazonaws.com buster main" | sudo tee /etc/apt/sources.list.d/ngrok.list
sudo apt update && sudo apt install ngrok

# Sign up at ngrok.com, get your token, then:
ngrok config add-authtoken YOUR_TOKEN
ngrok http 8001
```

Cloudflare Tunnel is recommended (no account needed for quick shares).

---

## Troubleshooting

| Problem                           | Fix                                                                                 |
| --------------------------------- | ----------------------------------------------------------------------------------- |
| "419 Page Expired" on forms       | Already handled (SESSION_DOMAIN=null). If it recurs, run `php artisan config:clear` |
| CSS/JS not loading                | `trustProxies('*')` is set — should auto-work. Clear cache if needed                |
| "Connection refused" in tunnel    | The PHP server isn't running. Check `/tmp/bagisto-serve.log`                        |
| Tunnel URL shows Cloudflare error | Your local server crashed — check the serve log                                     |
| QA can't log in                   | Make sure their account is verified (check Mailpit)                                 |

---

## Quick Reference

```bash
# Share with QA (all-in-one)
./share-qa.sh

# Stop sharing
Ctrl+C

# Just the dev environment (no public access)
./start-dev.sh
cd my-store && php artisan serve --port=8001

# View emails
http://localhost:8025
```
