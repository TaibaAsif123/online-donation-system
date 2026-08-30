# Part 2 — Donation Form (Member 2, Eiman Asmat)

## Files

```
online-donation-system/
├── donate.php          ← the form + server-side validation
├── payment.php         ← simulated payment step
├── confirmation.php    ← thank-you / receipt page
├── css/donate.css      ← styles for all three (reuses tokens from style.css)
└── js/donate.js        ← client-side validation
```

Nothing in `php/`, `database/` or `js/script.js` was changed.

## The flow

```
index.php  →  donate.php?cause_id=N  →  payment.php?donation_id=N  →  confirmation.php?donation_id=N
   card          form + validation        simulated gateway              receipt
                 INSERT … 'Pending'       UPDATE … 'Success'/'Failed'
```

Every step redirects (Post/Redirect/Get), so refreshing a page never donates twice.

## Checklist

| Requirement | Where |
|---|---|
| Pulls the latest code | Cloned `main`, which already had Member 3's DB layer and Member 1's homepage |
| Form fields: Name, Email, Amount, Cause, Payment Method | `donate.php` — plus an optional message and preset amount chips |
| JavaScript validation | `js/donate.js` — validates on blur, re-checks while typing once a field has errored, blocks submit and focuses the first bad field |
| PHP server-side validation | `donate.php` — runs on every POST regardless of JS; also a CSRF token check |
| Simulated payment step | `payment.php` — shows the total and method, "Pay" sets `payment_status = 'Success'`, "Cancel" sets `'Failed'`. No card or wallet details are asked for or stored |
| Connects to Member 3's insert function | `getOrCreateDonor()` and `insertDonation()` from `php/donation_functions.php` |
| Thank-you confirmation page | `confirmation.php` — reference number, amount, method, status, date, donor |
| Tested | Cause card → form opens with the right cause → submit → row appears in `donations` |

## Validation rules (same in JS and PHP)

| Field | Rule |
|---|---|
| Name | Required, 3–100 chars, letters/spaces/apostrophes/hyphens/dots only |
| Email | Required, valid format, max 150 chars |
| Amount | Required, numeric, Rs. 100 – Rs. 1,000,000 |
| Payment method | Required, must match the allowed list |
| Cause | Must exist in the `causes` table |
| Message | Optional, max 300 chars |

## Security notes for the viva

- Every query uses **prepared statements** — no string concatenation, so no SQL injection.
- Every value printed to the page goes through `htmlspecialchars()` — no XSS.
- A **CSRF token** is checked on both POSTs.
- `payment.php` and `confirmation.php` only show a donation whose id matches the one in the
  session, so changing the number in the URL cannot expose another donor's record.
- `payment.php` refuses to process a donation that is not `Pending`, so the status
  cannot be flipped twice.

## Testing it

1. Start Apache and MySQL in XAMPP, import `database/donation_system.sql`.
2. Open `http://localhost/online-donation-system/index.php`.
3. Click any cause card — the form opens with that cause selected.
4. Submit with empty fields to see the JS errors; disable JavaScript and submit again to
   see the same errors come back from PHP.
5. Complete a donation, then check phpMyAdmin:

```sql
SELECT d.donation_id, dn.name, c.cause_name, d.amount, d.payment_method, d.payment_status
FROM donations d
JOIN donors dn ON d.donor_id = dn.donor_id
JOIN causes c  ON d.cause_id = c.cause_id
ORDER BY d.donation_id DESC;
```

Cancel one payment too — it shows up as `Failed`, which gives Member 4's report more than
one status to display.
