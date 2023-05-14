# phpGridcoin

phpGridcoin is a RPC connector in PHP for the Gridcoin Research wallet. It connects to a local or remote wallet to fetch chain information.

**PLEASE BE AWARE**  that the current library is not working without the changes submitted to PR #209 on JSONMapper in https://github.com/cweiske/jsonmapper/pull/209



---
## Static Library
The code is written as a static Class, which means we set everything once, and no need to call more than once.


## Wallet Interactions

```
GridcoinWallet::$host = "localhost";
GridcoinWallet::$port = "25717";
GridcoinWallet::$user = "myWalletUser";
GridcoinWallet::$pass = "mySecretWalletPasswd";

// Get Current Block Count
$currentBlockCount = GridcoinWallet::getblockcount();
```

---
## Wallet Commands

See [Wallet Commands](https://github.com/startailcoon/phpGridcoin/issues/1)
