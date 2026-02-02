<?php

// DO NOT MODIFY. Files that call the config can use these defines
// Otherwise, copy them to any file that is called for the page you require constants. DO NOT insert directly into an array

// Filesize Constants - Start copy
define('KB', 1024);
define('MB', 1024 * KB);
define('GB', 1024 * MB);

// Time Constants
define('MINUTE', 60);
define('HOUR', 60 * MINUTE);
define('DAY', 24 * HOUR);
define('WEEK', 7 * DAY);
// End defines - End copy

$site_config = [];
$site_config['ttversion'] = '3.0'; //DONT CHANGE THIS!

// Main Site Settings
$site_config['SITENAME'] = 'Testing';					//Site Name
$site_config['SITEEMAIL'] = 'change@myemailsux.com';		//Emails will be sent from this address
$site_config['SITEURL'] = 'http://site.com';	//Main Site URL
$site_config['default_language'] = "1";						//DEFAULT LANGUAGE ID
$site_config['default_theme'] = "1";						//DEFAULT THEME ID
$site_config['CHARSET'] = "utf-8";						//Site Charset
$site_config['announce_list'] = "$site_config[SITEURL]/announce.php"; //seperate via comma
$site_config['MEMBERSONLY'] = true;							//MAKE MEMBERS SIGNUP
$site_config['MEMBERSONLY_WAIT'] = true;					//ENABLE WAIT TIMES FOR BAD RATIO
$site_config['ALLOWEXTERNAL'] = true;		//Enable Uploading of external tracked torrents
$site_config['UPLOADERSONLY'] = false;		//Limit uploading to uploader group only
$site_config['INVITEONLY'] = false;			//Only allow signups via invite
$site_config['ENABLEINVITES'] = true;		// Enable invites regardless of INVITEONLY setting
$site_config['CONFIRMEMAIL'] = false;		//Enable / Disable Signup confirmation email
$site_config['ACONFIRM'] = false;			//Enable / Disable ADMIN CONFIRM ACCOUNT SIGNUP
$site_config['ANONYMOUSUPLOAD'] = false;		//Enable / Disable anonymous uploads
$site_config['PASSKEYURL'] =  "$site_config[SITEURL]/announce.php?passkey=%s"; // Announce URL to use for passkey
$site_config['UPLOADSCRAPE'] = true; // Scrape external torrents on upload? If using mega-scrape.php you should disable this
$site_config['FORUMS'] = true; // Enable / Disable Forums
$site_config['FORUMS_GUESTREAD'] = false; // Allow / Disallow Guests To Read Forums
$site_config["OLD_CENSOR"] = false; // Use the old change to word censor set to true otherwise use the new one.   

$site_config['maxusers'] = 20000; // Max # of enabled accounts
$site_config['maxusers_invites'] = $site_config['maxusers'] + 5000; // Max # of enabled accounts when inviting

$site_config['currency_symbol'] = '$'; // Currency symbol (HTML allowed)

//AGENT BANS (MUST BE AGENT ID, USE FULL ID FOR SPECIFIC VERSIONS)
$site_config['BANNED_AGENTS'] = "-AZ21, -BC, LIME";

//PATHS, ENSURE THESE ARE CORRECT AND CHMOD TO 777 (ALSO ENSURE TORRENT_DIR/images is CHMOD 777)
$site_config['torrent_dir'] = getcwd().'/uploads';
$site_config['nfo_dir'] = getcwd().'/uploads';
$site_config['blocks_dir'] = getcwd().'/blocks';

// Image upload settings
$site_config['image_max_filesize'] = 512 * KB; // Max uploaded image size in bytes (Default: 512 kB)
$site_config['allowed_image_types'] = [
    "image/gif" => ".gif",    // GIF format
    "image/jpeg" => ".jpg",   // Standard JPEG format
    "image/png" => ".png",    // PNG format
    "image/webp" => ".webp"   // Modern WebP format
];

$site_config['SITE_ONLINE'] = true;									//Turn Site on/off
$site_config['OFFLINEMSG'] = 'Site is down for a little while';	

$site_config['WELCOMEPMON'] = true;			//Auto PM New members
$site_config['WELCOMEPMMSG'] = 'Thank you for registering at our tracker! Please remember to keep your ratio at 1.00 or greater :)';

$site_config['SITENOTICEON'] = true;
$site_config['SITENOTICE'] = 'Welcome To TorrentTrader v3.0';

$site_config['UPLOADRULES'] = 'You should also include a .nfo file wherever possible<br />Try to make sure your torrents are well-seeded for at least 24 hours<br />Do not re-release material that is still active';

//Setup Site Blocks
$site_config['LEFTNAV'] = true; //Left Column Enable/Disable
$site_config['RIGHTNAV'] = true; // Right Column Enable/Disable
$site_config['MIDDLENAV'] = true; // Middle Column Enable/Disable
$site_config['SHOUTBOX'] = true; //enable/disable shoutbox
$site_config['NEWSON'] = true;
$site_config['DONATEON'] = true;
$site_config['DISCLAIMERON'] = true;

//WAIT TIME VARS
$site_config['WAIT_CLASS'] = '1,2';		//Classes wait time applies to, comma seperated
$site_config['GIGSA'] = '1';			//Minimum gigs
$site_config['RATIOA'] = '0.50';		//Minimum ratio
$site_config['WAITA'] = '24';			//If neither are met, wait time in hours

$site_config['GIGSB'] = '3';			//Minimum gigs
$site_config['RATIOB'] = '0.65';		//Minimum ratio
$site_config['WAITB'] = '12';			//If neither are met, wait time in hours

$site_config['GIGSC'] = '5';			//Minimum gigs
$site_config['RATIOC'] = '0.80';		//Minimum ratio
$site_config['WAITC'] = '6';			//If neither are met, wait time in hours

$site_config['GIGSD'] = '7';			//Minimum gigs
$site_config['RATIOD'] = '0.95';		//Minimum ratio
$site_config['WAITD'] = '2';			//If neither are met, wait time in hours

//CLEANUP AND ANNOUNCE SETTINGS
$site_config['PEERLIMIT'] = '10000';			//LIMIT NUMBER OF PEERS GIVEN IN EACH ANNOUNCE
$site_config['autoclean_interval'] = 10 * MINUTE;		//Time between each auto cleanup
$site_config['LOGCLEAN'] = 28 * DAY;			// How often to delete old entries. (Default: 28 days)
$site_config['announce_interval'] = 15 * MINUTE;		//Announce Interval
$site_config['signup_timeout'] = 3 * DAY;		//Time a user stays as pending before being deleted
$site_config['maxsiteusers'] = '10000';			//Maximum site members
$site_config['max_dead_torrent_time'] = 6 * HOUR;//Time until torrents that are dead are set invisible

//AUTO RATIO WARNING
$site_config["ratiowarn_enable"] = true; //Enable/Disable auto ratio warning
$site_config["ratiowarn_minratio"] = 0.4; //Min Ratio
$site_config["ratiowarn_mingigs"] = 4;  //Min GB Downloaded
$site_config["ratiowarn_daystowarn"] = 14; //Days to ban

// category = Category Image/Name, name = Torrent Name, dl = Download Link, uploader, comments = # of comments, completed = times completed, size, seeders, leechers, health = seeder/leecher ratio, external, wait = Wait Time (if enabled), rating = Torrent Rating, added = Date Added, nfo = link to nfo (if exists)
$site_config["torrenttable_columns"] = "category,name,dl,uploader,comments,size,seeders,leechers,health,external";
// size, speed, added = Date Added, tracker, completed = times completed
$site_config["torrenttable_expand"] = "";

// Caching settings
$site_config["cache_type"] = "disk"; // disk = Save cache to disk, memcache = Use memcache, apc = Use APC, xcache = Use XCache
$site_config["cache_memcache_host"] = "localhost"; // Host memcache is running on
$site_config["cache_memcache_port"] = 11211; // Port memcache is running on
$site_config['cache_dir'] = getcwd().'/cache'; // Cache dir (only used if type is "disk"). Must be CHMOD 777


// Mail settings
// php to use PHP's built-in mail function. or pear to use http://pear.php.net/Mail
// MUST use pear for SMTP
$site_config["mail_type"] = "php";
$site_config["mail_smtp_host"] = "localhost"; // SMTP server hostname
$site_config["mail_smtp_port"] = "25"; // SMTP server port
$site_config["mail_smtp_ssl"] = false; // true to use SSL
$site_config["mail_smtp_auth"] = false; // true to use auth for SMTP
$site_config["mail_smtp_user"] = ""; // SMTP username
$site_config["mail_smtp_pass"] = ""; // SMTP password

// Password hashing - Once set, cannot be changed without all users needing to reset their passwords
$site_config["passhash_method"] = "sha1"; // Hashing method (sha1, md5 or hmac). Must use what your previous version of TT did or all users will need to reset their passwords
// Only used for hmac.
$site_config["passhash_algorithm"] = "sha1"; // See http://php.net/hash_algos for a list of supported algorithms.
$site_config["passhash_salt"] = ""; // Shouldn't be blank. At least 20 characters of random text.

die("You didn't edit your config correctly."); // You MUST remove this line
