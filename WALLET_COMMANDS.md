# Wallet Commands

These are the commands that we can handle through the script.

### Network
- `addmultisigaddress <nrequired> <'["key","key"]'> [account]`
- `addredeemscript <redeemScript> [account]`
- `backupprivatekeys`
- `backupwallet`
- `burn <amount> [hex string]`
- `checkwallet`
- `consolidatemsunspent <address> <block-start> <block-end> [max-grc] [max-inputs]`
- `consolidateunspent <address> [UTXO size] [maximum number of inputs] [sweep all addresses] [sweep change]`
- `createrawtransaction [{"txid":"id","vout":n},...] {"address":amount,"data":"hex",...}`
- `decoderawtransaction <hex string>`
- `decodescript <hex string>`
- `dumpprivkey <gridcoinaddress> [bool:dump hex]`
- `dumpwallet <filename>`
- `encryptwallet <passphrase>`
- `getaccount <gridcoinaddress>`
- `getaccountaddress <account>`
- `getaddressesbyaccount <account>`
- `getbalance ( "account" minconf includeWatchonly )`
- `getbalancedetail ( minconf includeWatchonly )`
- `getnewaddress [account]`
- `getnewpubkey [account]`
- `getrawtransaction <txid> [verbose=bool]`
- `getrawwallettransaction <txid>`
- `getreceivedbyaccount <account> [minconf=1]`
- `getreceivedbyaddress <Gridcoinaddress> [minconf=1]`
- [x] `gettransaction "txid" ( includeWatchonly )`
- `getunconfirmedbalance`
- `getwalletinfo`
- `importprivkey <gridcoinprivkey> [label] [bool:rescan]`
- `importwallet <filename>`
- `keypoolrefill [new-size]`
- `listaccounts ( minconf includeWatchonly)`
- `listaddressgroupings`
- `listreceivedbyaccount ( minconf includeempty includeWatchonly)`
- `listreceivedbyaddress ( minconf includeempty includeWatchonly)`
- `listsinceblock ( "blockhash" target-confirmations includeWatchonly)`
- `liststakes ( count )`
- `listtransactions ( "account" count from includeWatchonly)`
- `listunspent [minconf=1] [maxconf=9999999]  ["address",...]`
- `maintainbackups ( "retention by number" "retention by days" )`
- `makekeypair [prefix]`
- `move <fromaccount> <toaccount> <amount> [minconf=1] [comment]`
- `rainbymagnitude project_id amount ( trial_run output_details )`
- `repairwallet`
- `resendtx`
- `reservebalance [<reserve> [amount]]`
- `scanforunspent <address> <block-start> <block-end> [bool:export] [export-type]`
- `sendfrom <account> <gridcoinaddress> <amount> [minconf=1] [comment] [comment-to] [message]`
- `sendmany <fromaccount> {address:amount,...} [minconf=1] [comment]`
- `sendrawtransaction <hex string>`
- `sendtoaddress <gridcoinaddress> <amount> [comment] [comment-to] [message]`
- `setaccount <gridcoinaddress> <account>`
- `sethdseed ( "newkeypool" "seed" )`
- `settxfee <amount>`
- `signmessage <Gridcoinaddress> <message>`
- `signrawtransaction <hex string> [{"txid":txid,"vout":n,"scriptPubKey":hex},...] [<privatekey1>,...] [sighashtype="ALL"]`
- `upgradewallet [version]`
- `validateaddress <gridcoinaddress>`
- `validatepubkey <gridcoinpubkey>`
- `verifymessage <Gridcoinaddress> <signature> <message>`
- `walletdiagnose`

## Staking 

- `advertisebeacon ( force )`
- `beaconconvergence`
- `beaconreport <active only>`
- `beaconstatus [cpid]`
- `createmrcrequest [dry_run [force [fee]]]`
- `explainmagnitude ( cpid )`
- `getlaststake`
- `getstakinginfo`
- `getmrcinfo [detailed MRC info [CPID [low height [high height]]]]`
- `lifetime [cpid]`
- `magnitude <cpid>`
- `pendingbeaconreport`
- `resetcpids`
- `revokebeacon <cpid>`
- `superblockage`
- `superblocks [lookback [displaycontract [cpid]]]`

## Network

- `addnode <node> <add|remove|onetry>`
- `askforoutstandingblocks`
- `clearbanned`
- `currenttime`
- `getaddednodeinfo <dns> [node]`
- `getbestblockhash`
- `getblock <hash> [bool:txinfo]`
- `getblockbymintime <timestamp> [bool:txinfo]`
- [x] `getblockbynumber <number> [bool:txinfo]`
- `getblockchaininfo`
- [x] `getblockcount`
- `getblockhash <index>`
- [x] `getblocksbatch <starting block number or hash> <number of blocks> [bool:txinfo]`
- `getburnreport`
- `getcheckpoint`
- `getconnectioncount`
- `getdifficulty`
- `getinfo`
- `getnettotals`
- `getnetworkinfo`
- `getnodeaddresses [count]`
- `getpeerinfo`
- [x] `getrawmempool`
- `listbanned`
- `networktime`
- `ping`
- `setban <ip or subnet> <command> [bantime] [absolute]`
- `showblock <index>`
- `stop`

## Voting

- `addpoll <type> <title> <days> <question> <answer1;answer2...> <weighttype> <responsetype> <url> <required_field_name1=value1;required_field_name2=value2...>`
- `getpollresults <poll_title_or_id>`
- [x] `getvotingclaim <poll_or_vote_id>`
- `listpolls ( showfinished )`
- `DEPRECATED: vote <title> <answer1;answer2...>`
- `votebyid <poll_id> <choice_id_1> ( choice_id_2... )`
- `votedetails <poll_title_or_id>`