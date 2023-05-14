# phpGridcoin

phpGridcoin is a RPC connector in PHP for the Gridcoin Research wallet. It connects to a local or remote wallet to fetch chain information.


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

See [Wallet Commands](WALLET_COMMANDS.md)
