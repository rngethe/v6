# Begin Securitycheck Pro Default Blacklist
	if ($http_user_agent ~* "^BlackWidow"){ return 403; }
	if ($http_user_agent ~ "^Bolt"){ return 403; }
	if ($http_user_agent ~* "CazoodleBot"){ return 403; }
	if ($http_user_agent ~* "^ChinaClaw"){ return 403; }
	if ($http_user_agent ~* "^Custo"){ return 403; }
	if ($http_user_agent ~ "^Default"){ return 403; }
	if ($http_user_agent ~* "^DIIbot"){ return 403; }
	if ($http_user_agent ~* "^DISCo"){ return 403; }
	if ($http_user_agent ~* "discobot"){ return 403; }
	if ($http_user_agent ~* "^eCatch"){ return 403; }
	if ($http_user_agent ~* "ecxi"){ return 403; }
	if ($http_user_agent ~* "^EirGrabber"){ return 403; }
	if ($http_user_agent ~* "^EmailCollector"){ return 403; }
	if ($http_user_agent ~* "^EmailSiphon"){ return 403; }
	if ($http_user_agent ~* "^EmailWolf"){ return 403; }
	if ($http_user_agent ~* "^ExtractorPro"){ return 403; }
	if ($http_user_agent ~* "^EyeNetIE"){ return 403; }
	if ($http_user_agent ~* "^FlashGet"){ return 403; }
	if ($http_user_agent ~* "^GetRight"){ return 403; }
	if ($http_user_agent ~* "^GetWeb!"){ return 403; }
	if ($http_user_agent ~* "^Go!Zilla"){ return 403; }
	if ($http_user_agent ~* "^Go-Ahead-Got-It"){ return 403; }
	if ($http_user_agent ~* "^GrabNet"){ return 403; }
	if ($http_user_agent ~* "^Grafula"){ return 403; }
	if ($http_user_agent ~* "GT::WWW"){ return 403; }
	if ($http_user_agent ~* "heritrix"){ return 403; }
	if ($http_user_agent ~* "^HMView"){ return 403; }
	if ($http_user_agent ~* "HTTP::Lite"){ return 403; }
	if ($http_user_agent ~* "HTTrack"){ return 403; }
	if ($http_user_agent ~* "ia_archiver"){ return 403; }
	if ($http_user_agent ~* "IDBot"){ return 403; }
	if ($http_user_agent ~* "id-search"){ return 403; }
	if ($http_user_agent ~* "id-search.org"){ return 403; }
	if ($http_user_agent ~* "^InterGET"){ return 403; }
	if ($http_user_agent ~* "^InternetSeer.com"){ return 403; }
	if ($http_user_agent ~* "IRLbot"){ return 403; }
	if ($http_user_agent ~* "^Java"){ return 403; }
	if ($http_user_agent ~* "^JetCar"){ return 403; }
	if ($http_user_agent ~* "^larbin"){ return 403; }
	if ($http_user_agent ~* "^LeechFTP"){ return 403; }
	if ($http_user_agent ~* "libwww"){ return 403; }
	if ($http_user_agent ~* "libwww-perl"){ return 403; }
	if ($http_user_agent ~* "LinksManager.com_bot"){ return 403; }
	if ($http_user_agent ~* "linkwalker"){ return 403; }
	if ($http_user_agent ~* "lwp-trivial"){ return 403; }
	if ($http_user_agent ~* "^Maxthon$"){ return 403; }
	if ($http_user_agent ~* "MFC_Tear_Sample"){ return 403; }
	if ($http_user_agent ~* "^microsoft.url"){ return 403; }
	if ($http_user_agent ~ "Microsoft"){ return 403; }
	if ($http_user_agent ~* "^Mozilla.*Indy"){ return 403; }
	if ($http_user_agent ~* "^Mozilla.*NEWT"){ return 403; }
	if ($http_user_agent ~* "^MSFrontPage"){ return 403; }
	if ($http_user_agent ~* "^Navroad"){ return 403; }
	if ($http_user_agent ~* "^NearSite"){ return 403; }
	if ($http_user_agent ~* "^NetAnts"){ return 403; }
	if ($http_user_agent ~* "^NetSpider"){ return 403; }
	if ($http_user_agent ~* "^NetZIP"){ return 403; }
	if ($http_user_agent ~* "^Nutch"){ return 403; }
	if ($http_user_agent ~* "^Octopus"){ return 403; }
	if ($http_user_agent ~* "^PageGrabber"){ return 403; }
	if ($http_user_agent ~* "panscient.com"){ return 403; }
	if ($http_user_agent ~* "^pavuk"){ return 403; }
	if ($http_user_agent ~* "PECL::HTTP"){ return 403; }
	if ($http_user_agent ~* "^PeoplePal"){ return 403; }
	if ($http_user_agent ~* "^pcBrowser"){ return 403; }
	if ($http_user_agent ~* "PHPCrawl"){ return 403; }
	if ($http_user_agent ~* "PleaseCrawl"){ return 403; }
	if ($http_user_agent ~* "^psbot"){ return 403; }
	if ($http_user_agent ~* "^RealDownload"){ return 403; }
	if ($http_user_agent ~* "^ReGet"){ return 403; }
	if ($http_user_agent ~ "^Rippers"){ return 403; }
	if ($http_user_agent ~* "SBIder"){ return 403; }
	if ($http_user_agent ~* "^SeaMonkey$"){ return 403; }
	if ($http_user_agent ~* "^sitecheck.internetseer.com"){ return 403; }
	if ($http_user_agent ~* "^SiteSnagger"){ return 403; }
	if ($http_user_agent ~* "^SmartDownload"){ return 403; }
	if ($http_user_agent ~* "Snoopy"){ return 403; }
	if ($http_user_agent ~* "Steeler"){ return 403; }
	if ($http_user_agent ~* "^SuperBot"){ return 403; }
	if ($http_user_agent ~* "^SuperHTTP"){ return 403; }
	if ($http_user_agent ~* "^Surfbot"){ return 403; }
	if ($http_user_agent ~* "^tAkeOut"){ return 403; }
	if ($http_user_agent ~ "^Teleport"){ return 403; }
	if ($http_user_agent ~ "^Toata"){ return 403; }
	if ($http_user_agent ~* "URI::Fetch"){ return 403; }
	if ($http_user_agent ~* "urllib"){ return 403; }
	if ($http_user_agent ~* "User-Agent"){ return 403; }
	if ($http_user_agent ~* "^VoidEYE"){ return 403; }
	if ($http_user_agent ~* "webalta"){ return 403; }
	if ($http_user_agent ~* "^WebAuto"){ return 403; }
	if ($http_user_agent ~* "^[Ww]eb[Bb]andit"){ return 403; }
	if ($http_user_agent ~* "WebCollage"){ return 403; }
	if ($http_user_agent ~* "^WebCopier"){ return 403; }
	if ($http_user_agent ~* "^WebFetch"){ return 403; }
	if ($http_user_agent ~* "^WebLeacher"){ return 403; }
	if ($http_user_agent ~* "^WebReaper"){ return 403; }
	if ($http_user_agent ~* "^WebSauger"){ return 403; }
	if ($http_user_agent ~* "^WebStripper"){ return 403; }
	if ($http_user_agent ~* "^WebWhacker"){ return 403; }
	if ($http_user_agent ~* "^WebZIP"){ return 403; }
	if ($http_user_agent ~* "^Wget"){ return 403; }
	if ($http_user_agent ~* "^Widow"){ return 403; }
	if ($http_user_agent ~* "^WWW-Mechanize"){ return 403; }
	if ($http_user_agent ~* "^WWWOFFLE"){ return 403; }
	if ($http_user_agent ~* "zermelo"){ return 403; }
	if ($http_user_agent ~* "^Zeus"){ return 403; }
	if ($http_user_agent ~* "^Zeus.*Webster"){ return 403; }
	if ($http_user_agent ~* "ZyBorg"){ return 403; }	
	if ($http_user_agent ~* "Semrush"){ return 403; }
	if ($http_user_agent ~* "SiteBot"){ return 403; }
	if ($http_user_agent ~* "aipbot"){ return 403; }
	if ($http_user_agent ~* "BlackWidow"){ return 403; }
	if ($http_user_agent ~* "BackDoorBot"){ return 403; }
	if ($http_user_agent ~* "pixray"){ return 403; }
	if ($http_user_agent ~* "mindUpBot"){ return 403; }
	if ($http_user_agent ~* "SiteLockSpider"){ return 403; }
	if ($http_user_agent ~* "seoscanners"){ return 403; }
	if ($http_user_agent ~* "Exabot"){ return 403; }
	if ($http_user_agent ~* "WebRobot"){ return 403; }
	if ($http_user_agent ~* "Cliqzbot"){ return 403; }
	if ($http_user_agent ~* "AskPeterBot"){ return 403; }
	if ($http_user_agent ~* "BuddhaBot"){ return 403; }
	if ($http_user_agent ~* "MegaIndex.ru"){ return 403; }
	if ($http_user_agent ~* "SISTRIX"){ return 403; }
	if ($http_user_agent ~* "HttpConduitDownloade"){ return 403; }
	if ($http_user_agent ~* "BellPagesCA"){ return 403; }
	if ($http_user_agent ~* "Abonti"){ return 403; }
	if ($http_user_agent ~* "AdvBot"){ return 403; }
	if ($http_user_agent ~* "GarlikCrawler"){ return 403; }
	if ($http_user_agent ~* "HRCrawler"){ return 403; }
	if ($http_user_agent ~* "visionutils"){ return 403; }
	if ($http_user_agent ~* "TinEye"){ return 403; }
	if ($http_user_agent ~* "Nutch12"){ return 403; }
	if ($http_user_agent ~* "BLEXBot"){ return 403; }
	if ($http_user_agent ~* "webmeup-crawler"){ return 403; }
	if ($http_user_agent ~* "fr-crawler"){ return 403; }
	if ($http_user_agent ~* "Qwantify"){ return 403; }
	if ($http_user_agent ~* "beegoServer"){ return 403; }
	if ($http_user_agent ~* "BacklinkCrawler"){ return 403; }
	if ($http_user_agent ~* "linkdex"){ return 403; }
	if ($http_user_agent ~* "oBot"){ return 403; }
	if ($http_user_agent ~* "LoadTimeBot"){ return 403; }	
	if ($http_user_agent ~* "niki-bot"){ return 403; }
	if ($http_user_agent ~* "wonderbot"){ return 403; }
	if ($http_user_agent ~* "UnisterBot"){ return 403; }
	if ($http_user_agent ~* "Steeler"){ return 403; }
	if ($http_user_agent ~* "SeznamBot"){ return 403; }
	if ($http_user_agent ~* "LinkpadBo"){ return 403; }
	if ($http_user_agent ~* "Plukkie"){ return 403; }
	if ($http_user_agent ~* "SEOkicks"){ return 403; }
	if ($http_user_agent ~* "discoverybot"){ return 403; }
	if ($http_user_agent ~* "ImplisenseBot"){ return 403; }
	if ($http_user_agent ~* "Finderlein"){ return 403; }
	if ($http_user_agent ~* "WebWasher"){ return 403; }
	if ($http_user_agent ~* "AcoonBot"){ return 403; }
	if ($http_user_agent ~* "WeBoX"){ return 403; }
	if ($http_user_agent ~* "Wotbox"){ return 403; }
	if ($http_user_agent ~* "ELinks"){ return 403; }	
	if ($http_user_agent ~* "WebCopier"){ return 403; }
	if ($http_user_agent ~* "QCrawl"){ return 403; }
	if ($http_user_agent ~* "netEstate"){ return 403; }
	if ($http_user_agent ~* "Riddler"){ return 403; }
	if ($http_user_agent ~* "SafeSearch"){ return 403; }
	if ($http_user_agent ~* "ca-crawler"){ return 403; }
	if ($http_user_agent ~* "DotBot"){ return 403; }
	if ($http_user_agent ~* "WebCapture"){ return 403; }
	if ($http_user_agent ~* "Ezooms"){ return 403; }
	if ($http_user_agent ~* "WebZIP"){ return 403; }
	if ($http_user_agent ~* "izsearch"){ return 403; }
	if ($http_user_agent ~* "TurnitinBot"){ return 403; }
	if ($http_user_agent ~* "JoBo"){ return 403; }
	if ($http_user_agent ~* "SiteExplorer"){ return 403; }
	if ($http_user_agent ~* "Sleipnir"){ return 403; }	
	if ($http_user_agent ~* "OffByOne"){ return 403; }
	if ($http_user_agent ~* "NETCOMplete"){ return 403; }
	if ($http_user_agent ~* "Barca"){ return 403; }
	if ($http_user_agent ~* "SMTBot"){ return 403; }
	if ($http_user_agent ~* "SearchmetricsBot"){ return 403; }
	if ($http_user_agent ~* "BLEXBot"){ return 403; }
	if ($http_user_agent ~* "pixraybot"){ return 403; }
	if ($http_user_agent ~* "Powermarks"){ return 403; }
	if ($http_user_agent ~* "rogerbot"){ return 403; }
	if ($http_user_agent ~* "memorybot"){ return 403; }	
# End Securitycheck Pro Default Blacklist
