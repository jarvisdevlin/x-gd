![x-gd](/README_banner.png)  
**This is a brand new private server core for Geometry Dash and is a huge work in progress.**  
This pretty decent project aims to be better then all the other alternatives.
## Supported Versions
- 1.0
## Features
- SQLite3 (Lighter then MySQL!)
- New Dashboard (Easy to use!)
- Account Registration (even on 1.x!)
## How to Use
- When in-game, you can register by choosing your in-game name and uploading a level called "register me".
## Installation
### Requirements
- PHP Web Server (tested only on PHP8)
- SQLite3 for PHP
- Common Sense
### Setup
1. Get gd-x
```bash
git clone https://github.com/jarvisdevlin/gd-x.git
cd gd-x
```

2. Setup Database
```bash
sqlite3 database.db
sqlite3 database.db < database.sql
```

3. Setup Webserver
- WARNING: You should also use Apache if you plan to use .htaccess or anything else.
- Run Apache from XAMPP or whatever you use and access http://localhost:[YOUR PORT] to see if it works.

4. Patch Geometry Dash
 - [Same instructions as usual.](https://github.com/Cvolton/GMDprivateServer/wiki/Creating-Windows,-Android-and-IOS-Apps#ios-21-and-below)

## Issues
- updateGJLevel does nothing due to nothing about it that I can find.
- rateGJLevel does nothing, need ideas for something useful.
- Changing your name after registration in-game prevents you from uploading levels, for security reasons.
- The featured ring doesn't appear around featured levels, no matter what I do it just crashes the game.