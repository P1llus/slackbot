# Some bot

This is an implementation of [Botman.io](https://botman.io/) used for testing bot capabilities and API call intergration towards various security research websites.

## Current public modules added:

* Help Module

-- Display help commands

* Virustotal Module

-- Check IP Addresses for malicious activity
-- Check URL for malicious activity
-- Gather reports and list them in the channel/message

* Haveibeenpwned Module

-- Check a nickname or user account related to any data breaches
-- Check an email address and list which sources currently include that email in leaks

* Shodan

-- Display information about single IP, ports, company names, vulnerabilities and such
-- Run Shodan specific queries, and return with results
-- Check for heartbleed towards specified site
-- Check known vulnerabilities towards specified site
-- List commonly used search queries

* WPSCAN DB

-- Search for known vulnerabilities related to specific wordpress versions
-- Search for known vulnerabilities related to specific wordpress plugins

## Config

There is currently one config in each module, for API keys, and one main config file in Config.
These config files would need to be renamed from config.example.php

## Current public commands

```!vt ip [IP Address, example 192.168.1.1] - Checks virustotal if the IP address is safe```

```!vt url [URL, example www.microsoft.com] - Checks virustotal if the URL is safe```

```!wpscan version [VERSION, example 4.3] - Lists all vulnerabilities to a specific wordpress version]```

```!wpscan plugin [PLUGIN, example eshop] - Lists all vulnerabilities related to a specific wordpress plguin]```

```!shodan ip [IP Address, example 10.0.0.0] - Lists information from Shodan about the IP```

```!shodan query [QUERY, example hostname:something] - Uses all the same queries as Shodan does, gives you a list of IP addresses in return, that matches this query```

```!shodan heartbleed [IP, example 10.0.0.0] - Returns if target is vulnerable to Heartbleed```

```!shodan vuln [IP, example 100.10.0.0] - Returns all known vulnerabilties for IP address```

```!shodan listqueries - Returns popular queries that is used on Shodan```

```!haveibeenpwned account [Account name, example Terminator] - This checks ihasbeenpwned if your Username/Account name has ever been mentioned in any password leaks```

```!haveibeenpwned email [EMAIL, example test@example.com] - This checks ihasbeenpwned if your Email account is mentioned in any password leaks```

```!help - Returns this list```

## TODO

### Security

* Add possibility to upload files to virustotal
* Add support for malwr.com when it is up again, for really handy binary analysis

### General

* Make reports use the pastebin helper, to minimize spam in channels
* Possibility to load/reload modules on the fly, without a restart
* Authentication system
* Add a better way to PM users that executes command
* Implement fallback method for unknown commands
* SQL logging
* Look at possibility to load help commands directly from modules
* Update function, to look for new versions of a module
* Implement tests for each module
* Make scaffolding and document how to make modules
* Make a better README

### Sysadmin

* SYSADMIN Modules (on the way)

### Streaming

* Streaming module (For things like twitch.tv)

### Other

* Have one git per module instead
* Move all new features over to issues
* XMPP Driver?
