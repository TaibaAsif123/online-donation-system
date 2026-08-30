# Part 2 — Category Selection (Member 1)

Frontend only. No PHP logic, no database queries — exactly as scoped.

## Files to add to the repo

```
online-donation-system/
├── index.php          ← replace / add (homepage + category cards)
└── css/
    └── style.css      ← add
```

Drop both into the cloned repo root, commit, push. Nothing else is touched, so
this can't break Member 3's database code.

## Adding it to the given GitHub project

```bash
cd C:\xampp\htdocs\online-donation-system
git pull
# copy index.php and the css folder in here
git add index.php css/style.css
git commit -m "Part 2: homepage with category selection (Member 1)"
git push
```

Then open `http://localhost/online-donation-system/index.php`.

## The contract for Member 2 (donation form)

Each card links to `donate.php` with the cause ID in the query string:

| Card              | Link                     | `causes.cause_id` |
|-------------------|--------------------------|-------------------|
| Education         | `donate.php?cause_id=1`  | 1                 |
| Health            | `donate.php?cause_id=2`  | 2                 |
| Food              | `donate.php?cause_id=3`  | 3                 |
| Emergency Relief  | `donate.php?cause_id=4`  | 4                 |

`donate.php` should read it and pre-select the cause:

```php
$cause_id = isset($_GET['cause_id']) ? (int) $_GET['cause_id'] : 0;
// validate against the causes table before using it
```

**If Member 3's seed rows came out in a different order, change the four `href`
values in `index.php` — that's the only thing that needs editing.** Check with:

```sql
SELECT cause_id, cause_name FROM causes ORDER BY cause_id;
```

Member 4's page is linked as `admin/report.php` in the nav and footer — adjust
those two links if it ends up at a different path.

## Notes

- Zero external dependencies: no Bootstrap CDN, no Google Fonts, no icon
  library. Icons are inline SVG, so it renders identically offline.
- Responsive down to 360px; card grid reflows 4 → 2 → 1 columns.
- Keyboard accessible with visible focus rings; cards lift on `:focus-within`.
- `<?php echo date('Y'); ?>` in the footer is the only PHP on the page — it just
  prints the year. Rename to `index.html` and delete that line if a pure-HTML
  file is preferred.
- Brand name "HopeFund" is a placeholder — find and replace if the group picked
  something else.

## Test before pushing

1. Apache running, page loads at `http://localhost/online-donation-system/index.php`
2. All four cards hover, and each link shows the right `cause_id` in the URL bar
3. Resize the window to phone width — nav scrolls, cards stack
4. Tab through the page — every link gets a visible outline
