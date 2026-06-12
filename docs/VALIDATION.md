# Validation

Before consent, the expected GTM script count is:

```javascript
document.querySelectorAll('script[src*="googletagmanager.com/gtm.js"]').length
```

Expected result:

```text
0
```

After Accept all, the expected result is:

```text
1
```
