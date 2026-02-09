# CashTrack Workers

## Purpose

CashTrack is a **100% LIMIT-BASED** system. All restrictions are enforced at creation time, NOT by time.

## How Limits Work

### Trial Plan (Free)

- 50 transactions maximum
- 2 wallets maximum
- **Lifetime access** until limits reached
- User can't create 51st transaction or 3rd wallet
- Must upgrade to continue

### Paid Plans

- Basic: 500 transactions, 3 wallets
- Standard: 1000 transactions, 4 wallets
- Premium: 2000 transactions, 5 wallets
- All plans: **Lifetime access** (one-time payment)

## Worker Role

The `worker.php` script is **OPTIONAL** and currently does nothing critical because:

1. **No time-based expiry** - Plans don't expire by date
2. **No monthly resets** - Transaction limits are lifetime
3. **No notifications needed** - User sees usage in topbar
4. **Limits auto-enforced** - Can't create entries beyond limit

### Optional Uses

- Database cleanup (old logs, deleted users)
- Backup automation
- Custom maintenance tasks

## Limit Enforcement

Limits are enforced in `includes/limit_functions.php`:

```php
can_create_wallet($user_id)    // Checks wallet limit
can_create_transaction($user_id) // Checks transaction limit
create_transfer($user_id, ...)   // Double-entry accounting
```

When user tries to exceed limit → **Error shown immediately**

## Running Worker (Optional)

```bash
# Via cron (optional, for maintenance only)
*/30 * * * * cd /path/to/cashtrack && php workers/worker.php >> workers/worker.log 2>&1
```

## Key Difference from Monthly SaaS

| Monthly SaaS            | CashTrack (Limit-Based) |
| ----------------------- | ----------------------- |
| Expires after 30 days   | Never expires           |
| Resets monthly          | One-time limit          |
| Needs worker for expiry | No worker needed        |
| Subscription recurring  | One-time payment        |

## User Experience

1. User signs up → Gets Trial (50/2)
2. Creates 50 transactions → **Can't create more**
3. Sees "Limit Reached" in topbar
4. Upgrades to Basic (500/3)
5. Continues using → **No expiry worries**
