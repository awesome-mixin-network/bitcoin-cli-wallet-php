# How to trade bitcoin through PHP language

[Chinese](https://github.com/awesome-mixin-network/bitcoin-cli-wallet-php/blob/master/README-zhchs.md)

## Solution One: pay to ExinCore API
[Exincore](https://github.com/exinone/exincore) provide a commercial trading API on Mixin Network.

You pay USDT to ExinCore, ExinCore transfer Bitcoin to you on the fly with very low fee and fair price. Every transaction is anonymous to public but still can be verified on blockchain explorer. Only you and ExinCore know the details.

ExinCore don't know who you are because ExinCore only know your client's uuid.

### Pre-request:
You should already have created a wallet based on Mixin Network. Create one by reading [PHP Bitcoin tutorial](https://github.com/wenewzhang/mixin_labs-php-bot).

#### Install required packages
As you know, we introduce you the **mixin-sdk-php** in [Chapter 1](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README.md), assume it has installed before, let's install **uuid, msgpack** here.

- If you are want to know how to install PHP & composer,  [Chapter 1](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README.md)
- If you clone this repository, issue **composer install** to install dependency packages.
```bash
  composer require ramsey/uuid
  composer require rybakit/msgpack
```
### Generate parameter of your app in dashboard
After app is created in dashboard, you still need to [generate parameter](https://mixin-network.gitbook.io/mixin-network/mixin-messenger-app/create-bot-account#generate-secure-parameter-for-your-app)
and write down required content, these content will be written into config.php file.

#### Create Bitcoin wallet
The config.php contents like below:
```php
<?php
return [
    'client_id'     => 'a1ce2967-a534-417d-bf12-c86571e4eefa',
    'session_id'    => 'fe4ff62a-ce5a-4db3-9f53-3f365a260541',
    'private_key'   => <<<EOF
-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQCEvNjdA/Vk/yn2l0fHU/+IvOp8iYWnIEl//tGRS0tLFjF/Dtm5
+a6PtEx33nhGuiyLA4kDEBIEECOfD5nLy099CRzKiVjMbl1b11M/oNm1MJdmWjSg
umKrvBCac8oLPLdnb+xnqTZofrF84UbZjuZ4EGMyEs5Daw0NUPE1tJaA2wIDAQAB
AoGAd3cgF4tAiS2+LtnpGFVZX0/oGz4KtGgyvqgxJXuGwIWw9pH/E4rMuTmkuR3Z
Uo6tGFCON9t224FLDhWUbf8GAk5Uj/+WXiWVKmHsKtn93axkRhkxuXrv+REWOE6J
6XdGb5K6Fx2ud2HZ1L/CEADCoDLCq4OjFG7ey4c/fIycpkECQQDFzEsTQgogMYlX
iBQMP6l075AQuhC5o1VKlOgO1/7Irbq1M4P2GxQNFGVvyUE74yQSM/FAhCCHfac3
1wLbvYuXAkEAq8uyPZHbFEbEIOgbaImN/1qJY+/oqSyNriT1APkqnvQnDCLrlRVV
+FyV5C4eeZyVa00WFj7IOSJUMLwEnYDtXQJBAI449eZz/rnlRH7Wzqt7/wmg07Lj
RvFkOwi0hyNdNcrv+CcgUotcLw+0kbdOO4SnLyGTja25E3a458qj5F9CLCMCQCTE
YY8/yg1a39rTEhqbZeKCs+jJjZe3S1M74Zult/Nw+XJlftnXSSDwX7wICsmoM2pV
gyabpSplKHONqcczspkCQCDsmsgMP5ETBEDY1tqpa4YEgGB/rzizFuPWlzHm8OzA
g5w07wY/SeuA4BqDoNIycGK9OewtRlcbJmLLpx3F/DM=
-----END RSA PRIVATE KEY-----
EOF
    ,  //import your private_key
];
```
Create a new Bitcoin wallet, then save it to mybitcoin_wallet.csv!
```php
$mixinSdk_BotInstance = new MixinSDK(require './config.php');
$wallet_info = $mixinSdk_BotInstance->Network()->createUser("My Bitcoin Wallet");
print_r($wallet_info);

$wallet_Config = array();
$wallet_Config["private_key"] = $wallet_info["priKey"];
$wallet_Config["pin_token"]   = $wallet_info["pin_token"];
$wallet_Config["session_id"]  = $wallet_info["session_id"];
$wallet_Config["client_id"]   = $wallet_info["user_id"];

$csvary = array($wallet_Config);
$fp = fopen('mybitcoin_wallet.csv', 'a');
foreach ($csvary as $fields) {
    fputcsv($fp, $fields);
}
fclose($fp);
```
For the wallet's safety, set the file **mybitcoin_wallet.csv** read only!

- **chmod 400 mybitcoin_wallet.csv** Set mybitcoin_wallet.csv read only!

```bash
$ chmod 400 mybitcoin_wallet.csv
$ ls -la mybitcoin_wallet.csv
-r--------  1 wenewzhang  staff  1173 Mar 15 17:05 mybitcoin_wallet.csv

```

#### Deposit USDT or Bitcoin into your Mixin Network account and read balance
ExinCore can exchange between Bitcoin, USDT, EOS, Eth etc. Here show you how to exchange between USDT and Bitcoin,
Check the wallet's balance & address before you make order.

- Check the address & balance, remember it Bitcoin wallet address.
- Deposit Bitcoin to this Bitcoin wallet address.
- Check Bitcoin balance after 100 minutes later.
**By the way, Bitcoin & USDT 's address are the same.**

```php
if ($line == '2') {
  if (($handle = fopen("new_users.csv", "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    $mixinSdk_eachAccountInstance = new MixinSDK(GenerateConfigByCSV($data));
    $asset_info = $mixinSdk_eachAccountInstance->Wallet()->readAsset(BTC_ASSET_ID);
    print_r("Bitcoin wallet address is :".$asset_info["public_key"]."\n");
    print_r("Bitcoin wallet balance is :".$asset_info["balance"]."\n");
  }
    fclose($handle);
  } else print("Create user first\n");
}
if ($line == '3') {
  if (($handle = fopen("new_users.csv", "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    $mixinSdk_eachAccountInstance = new MixinSDK(GenerateConfigByCSV($data));
    $asset_info = $mixinSdk_eachAccountInstance->Wallet()->readAsset(USDT_ASSET_ID);
    print_r("USDT wallet address is :".$asset_info["public_key"]."\n");
    print_r("USDT wallet balance is :".$asset_info["balance"]."\n");
  }
    fclose($handle);
  } else print("Create user first\n");
}
```
#### Read market price
How to check the coin's price? You need understand what is the base coin. If you want buy Bitcoin and sell USDT, the USDT is the base coin. If you want buy USDT and sell Bitcoin, the Bitcoin is the base coin.
```php
function getExchangeCoins($base_coin) :string {
  $client = new GuzzleHttp\Client();
  $res = $client->request('GET', 'https://exinone.com/exincore/markets?base_asset='.$base_coin, [
      ]);
  $result = "";
  if ($res->getStatusCode() == "200") {
    // echo $res->getStatusCode() . PHP_EOL;
    $resInfo = json_decode($res->getBody(), true);
    echo "Asset ID | Asset Symbol | Price | Amount | Exchanges" . PHP_EOL;
    $result = "Asset ID | Asset Symbol | Price | Amount | Exchanges" . PHP_EOL;
    foreach ($resInfo["data"] as $key => $coinInfo) {
      echo ($coinInfo["exchange_asset"] ." ".$coinInfo["exchange_asset_symbol"]. "/". $coinInfo["base_asset_symbol"] .
            " ". $coinInfo["price"] ." ". $coinInfo["minimum_amount"] ."-". $coinInfo["maximum_amount"] . " ");
      $result .= $coinInfo["exchange_asset_symbol"]. "/". $coinInfo["base_asset_symbol"] .
                  " ". $coinInfo["price"] ." ". $coinInfo["minimum_amount"] ."-". $coinInfo["maximum_amount"] . " ";
      foreach ($coinInfo["exchanges"] as $key => $exchange) {
        echo $exchange . " ";
        $result .= $exchange . " ";
      }
      echo PHP_EOL;
      $result .= PHP_EOL;
    }
  }
  return $result;
}
```

#### Create a memo to prepare order
The chapter two: [Echo Bitcoin](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README2.md) introduce transfer coins. But you need to let ExinCore know which coin you want to buy. Just write your target asset into memo.
```php
$memo = base64_encode(MessagePack::pack([
                     'A' => Uuid::fromString($_targetAssetID)->getBytes(),
                     ]));
```
#### Pay BTC to API gateway with generated memo
Transfer Bitcoin(BTC_ASSET_ID) to ExinCore(EXIN_BOT), put you target asset uuid in the memo, otherwise, ExinCore will refund you coin immediately!
```php
const EXIN_BOT        = "61103d28-3ac2-44a2-ae34-bd956070dab1";
const BTC_ASSET_ID    = "c6d0c728-2624-429b-8e0d-d9d19b6592fa";
const EOS_ASSET_ID    = "6cfe566e-4aad-470b-8c9a-2fd35b49c68d";
const USDT_ASSET_ID   = "815b0b1a-2764-3736-8faa-42d694fa620a";

function coinExchange ($config, $_assetID, $_amount, $_targetAssetID) {
  $memo = base64_encode(MessagePack::pack([
                       'A' => Uuid::fromString($_targetAssetID)->getBytes(),
                       ]));
  echo PHP_EOL . $memo . PHP_EOL;
  // $mixinSdk_eachAccountInstance= new MixinSDK(GenerateConfigByCSV($data));
  $mixinSdk_eachAccountInstance= new MixinSDK($config);
  $transInfo = $mixinSdk_eachAccountInstance->Wallet()->transfer($_assetID,
                EXIN_BOT,$config['pin'],$_amount,$memo);
  print_r($transInfo);
}
```

If you want sell Bitcoin buy USDT, call it like below:
```php
coinExchange($config,BTC_ASSET_ID,"0.0001",USDT_ASSET_ID);
```

If you want sell USDT buy Bitcoin, call it like below:

```php
coinExchange($config,USDT_ASSET_ID,"1",BTC_ASSET_ID);
```

The ExinCore should transfer the target coin to your bot, meanwhile, put the fee, order id, price etc. information in the memo, unpack the data like below.
- **readUserSnapshots** Read snapshots of the user.
```php
$limit        = 20;
$offset       = '2019-03-10T01:58:25.362528Z';
$snapInfo = $mixinSdk_BotInstance->Wallet()->readUserSnapshots($limit, $offset);
// print_r($networkInfo2);
foreach ($snapInfo as  $record) {
  // echo $key . PHP_EOL;
  // print_r($record);
  if ($record['amount'] > 0 and $record['memo'] != '') {
    echo "------------MEMO:-coin--exchange--------------" . PHP_EOL;
    echo "memo: " . $record['memo'] . PHP_EOL;
    // print_r($dtPay->memo);
    echo "You Get Coins: ". $record['asset_id']. " " . $record['amount'] . PHP_EOL;
    $memoUnpack = MessagePack::unpack(base64_decode($record['memo']));
    $feeAssetID = Uuid::fromBytes($memoUnpack['FA'])->toString();
    $OrderID    = Uuid::fromBytes($memoUnpack['O'])->toString();
    if ($memoUnpack['C'] == 1000) {
      echo "Successful Exchange:". PHP_EOL;
      echo "Fee asset ID: " . $feeAssetID . " fee is :" . $memoUnpack['F'] . PHP_EOL;
      echo "Order ID: " . $OrderID . " Price is :" . $memoUnpack['P'] . PHP_EOL;
    } else print_r($memoUnpack);
    echo "--------------memo-record end---------------" . PHP_EOL;
  }
}
```

If you coin exchange successful, console output like below:
```bash
------------MEMO:-coin--exchange--------------
memo: hqFDzQPooVCnMzg3Mi45N6FGqTAuMDAwNzc0NqJGQcQQgVsLGidkNzaPqkLWlPpiCqFUoUahT8QQIbfeL6p5RVOcEP0mLb+t+g==
You Get Coins: 815b0b1a-2764-3736-8faa-42d694fa620a 0.3857508
Successful Exchange:
Fee asset ID: 815b0b1a-2764-3736-8faa-42d694fa620a fee is :0.0007746
Order ID: 21b7de2f-aa79-4553-9c10-fd262dbfadfa Price is :3872.97
--------------memo-record end---------------
```

#### Read Bitcoin balance
Check the wallet's balance.
```php
$mixinSdk = new MixinSDK(require './config.php');
$asset_info = $mixinSdk->Wallet()->readAsset(USDT_ASSET_ID);
print_r("USDT wallet balance is :".$asset_info["balance"]."\n");
```

## Source code usage
Execute **php call_apis.php** to run it.

- 1 : Create Bitcoin Wallet and update PIN
- 2 : Read Bitcoin balance & address
- 3 : Read USDT balance & address
- 6 : Transfer Bitcoin from bot to new user
- qu: Read market price(USDT)
- qb: Read market price(BTC)
- b : Balance of  bot (USDT & BTC)
- s : Read Snapshots
- tb: Transfer 0.0001 BTC buy USDT
- tu: Transfer $1 USDT buy BTC
- we: Withdrawal EOS
- wb: Withdrawal BTC
- q : Exit

[Full source code](https://github.com/awesome-mixin-network/bitcoin-cli-wallet-php/blob/master/bitcoin-cli-wallet.php)

## Solution Two: List your order on Ocean.One exchange
