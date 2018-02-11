
# w.filebackup

A helper script writed in php for make fast backups from remote server for administrators.


## Advantage

- It is compatible with php servers with safe mode in ON.
- It is compatible with php servers without zlib or gz.
- Not need a native shell into Operative System.
- It is compatible with Apache servers with mod-evasive and mod-security
- It is compatible with firewall Antivirus, antiShells, WAF, etc.


## How to use?

Upload the php file to server and run the backup from wget, the script simulate a Apache index directory with hidden and restricted files:

    $ mkdir page && cd page
    $ wget -r -x -nH -np --cut-dirs=1 --no-check-certificate --post-data 'token=1' -e robots=off -U 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:5.0) Gecko/20100101 Firefox/5.02011-10-16' http://example.com/w.filebackup.php

Use `--post-data 'token=1'` for prevent download the parent directory. The user-agent header is very important to prevent WAF detection.

For more help, see the source code or write a email to [me](mailto:whk@elhacker.net)


## The Google Dork

Only if you lost the url for your website: `inurl:".php?do=/" + intitle:"index of"`


## How to contribute?

|METHOD                 |WHERE                                                                                        |
|-----------------------|---------------------------------------------------------------------------------------------|
|Donate                 |[Paypal](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=KM2KBE8F982KS) |
|Find bugs              |Using the [Issues tab](https://github.com/WHK102/MyNetwork/issues)                              |
|Providing new ideas    |Using the [Issues tab](https://github.com/WHK102/MyNetwork/issues)                              |
|Creating modifications |Using the [Pull request tab](https://github.com/WHK102/MyNetwork/pulls)                         |
