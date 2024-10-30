=== Pay With CoinsPaid for WooCommerce – Cryptocurrency Payment Gateway ===
Requires at least: 4.6
Tested up to: 6.0.1
Stable tag: 1.0.3
License: GPLv2 or later

== Description ==

CoinsPaid plugin allows you to accept payments in cryptocurrency. CoinsPaid supports over **50 cryptocurrencies** and counting.

### Features

CoinsPaid plugin allows you to accept payments in cryptocurrency. CoinsPaid supports over **50 cryptocurrencies** and counting.
Users will be able to pick a crypto of their choice and then scan a QR code of your crypto wallet address. After that, they will send their crypto currency to your account in CoinsPaid.

CoinsPaid provides **сryptocurrency payment** services, enabling businesses to operate worldwide.

### Supported crypto currencies

*BTC - Bitcoin*
*LTC - Litecoin*
*BCH - Bitcoin cash*
*ADA - Cardano*
*ETH - Ethereum*
*DOGE - Dogecoin*
*NEO - Neo*
*XRP - Ripple*
*CSC - CasinoCoin (based on XRP network)*
*USDT - Tether* USD Omni layer token (based on BTC network)*
*USDTE - Tether USD ERC-20 token*
*USDTT - Tether USD TRC-20 token*
*ERC20 token(s) can be added by request*
*BNB - Binance BEP-2 coin*
*BNB-BSC - Binance BEP-20 coin*
*BUSD - Binance USD BEP-20 token*
*SNACK - Crypto Snack BEP-20 token*
*EURS - STASIS EURS*
*USDC - USD Coin ERC-20 token*
*TRX - TRON*
*XED - Exeedme ERC-20 token*
*DAI - Dai ERC-20 Stablecoin*
*MRX - Metrix Coin*
*WBTC - Wrapped Bitcoin*
*CPD - CoinsPaid ERC-20 token*
*BRZ - Brazilian Digital ERC-20 token*

*\* Tether token is the most popular "stablecoin", the price of the token is fluctuating around 1 USD.*

### Supported networks

*ERC-20 means that a token is based on the Ethereum network;*
*TRC-20 means that a token is based on the Tron network;*
*BEP-20 means that a token is based on Binance Smart Chain network;*
*BEP-2 means that a coin or token is based on the Binance Chain network.*

### Third Party API & Licence Information

CoinsPaid Website: [https://coinspaid.com/](https://coinspaid.com/)
API docs: [https://docs.cryptoprocessing.com/](https://docs.cryptoprocessing.com/?_ga=2.121967619.983919275.1664180307-416060835.1664180307)
Privacy policy: [https://coinspaid.com/privacypolicy/](https://coinspaid.com/privacypolicy/)
Term of Use: [https://coinspaid.com/terms-of-use/](https://coinspaid.com/terms-of-use/)

== Installation ==

To use Plugin, you must first operate with CoinsPaid Back Office.

### WooCommerce Backoffice

1. Download the *“Pay With CoinsPaid for WooCommerce – Cryptocurrency Payment Gateway”* plugin - an unzipped folder:
2. Activate the plugin and make sure that it is activated in the list of plugins.
3. Go to the WooCommerce payment settings (WooCommerce then click on the Settings section and then choose Payments).
4. Find the *“Pay With CoinsPaid for WooCommerce – Cryptocurrency Payment Gateway”* plugin in the list of available providers, activate it and click on a Set Up button.

### CoinsPaid Backoffice

1. Go to a Merchant's Settings and open an *Api keys* menu.

**Attention!** Please note, for your safety, we recommend creating a new Merchant to work with the plugins.
2. To create an *Api key* you need to  click on a *Generate Api key* button, enter the 2FA code and then click on the Generate button.
3. To activate a new API key click on the *Activate* button.
4. Enter the 2FA code again and click on the *Activate* button again.

**Attention!** Please note that the secret key is displayed only once when it is activated. Therefore, it is required to save it for further work.
5. Go to the Merchant settings, then go to the API section and setup the callback URL like:

http://your_site_url/wp-json/coinspaid/v1/webhook

**Attention!** *Your_site_url* shall match with an url in the WooCommerce extension settings. This is required for updating the operation status in the administrative panel of the WooCommerce plugin.

### WooCommerce Backoffice
1. Insert the secret key in the Secret Key field and the public key in the Publishable Key field and save the settings.
2. Accept payments in Bitcoin, Ethereum, USDT and other digital currencies with a leading crypto payment gateway from CoinsPaid.

== Changelog ==

<strong>**Version 1.0.3 | 06 Oct 2022**</strong>
<pre>
Improved: Installation guide.
</pre>

<strong>**Version 1.0.2 | 28 Sep 2022**</strong>
<pre>
Improved: Readme.
</pre>

<strong>**Version 1.0.1 | 28 Sep 2022**</strong>
<pre>
Improved: Readme.
</pre>

<strong>**Version 1.0.0 | 30 Aug 2022**</strong>
<pre>
New: Initial plugin release.
</pre>
