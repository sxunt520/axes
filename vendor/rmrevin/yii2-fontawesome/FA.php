<?php
/**
 * FA.php
 * @author Revin Roman
 * @link https://rmrevin.ru
 */

namespace rmrevin\yii\fontawesome;

/**
 * Class FA
 * @package rmrevin\yii\fontawesome
 */
class FA extends FontAwesome
{

    /**
     * Get all icon constants for dropdown list in example
     * @param bool $html whether to render icon as array value prefix
     * @return array
     */
    public static function getConstants($html = false)
    {
        $result = [];
        foreach ((new \ReflectionClass(get_class()))->getConstants() as $constant) {
            $key = static::$cssPrefix . ' ' . static::$cssPrefix . '-' . $constant;

            $result[$key] = ($html)
                ? static::icon($constant) . '&nbsp;&nbsp;' . $constant
                : $constant;
        }

        return $result;
    }

    /**
     * How I saved from: http://fortawesome.github.io/Font-Awesome/cheatsheet/
     *
     * $.each($('.col-md-4.col-sm-6.col-lg-3').text().split(' '), function (i, item) {
     *   if(item.indexOf('fa-') !== -1) {
     *     var icon = item.substr(3).replace(/(\n)/g, '');
     *     console.log('const _' + icon.replace(/-/gi, '_').toUpperCase() + " = '" + icon + "';")
     *   }
     * });
     */

    const _500PX = '500px';
    const _ADJUST = 'adjust';
    const _ADN = 'adn';
    const _ALIGN_CENTER = 'align-center';
    const _ALIGN_JUSTIFY = 'align-justify';
    const _ALIGN_LEFT = 'align-left';
    const _ALIGN_RIGHT = 'align-right';
    const _AMAZON = 'amazon';
    const _AMBULANCE = 'ambulance';
    const _ANCHOR = 'anchor';
    const _ANDROID = 'android';
    const _ANGELLIST = 'angellist';
    const _ANGLE_DOUBLE_DOWN = 'angle-double-down';
    const _ANGLE_DOUBLE_LEFT = 'angle-double-left';
    const _ANGLE_DOUBLE_RIGHT = 'angle-double-right';
    const _ANGLE_DOUBLE_UP = 'angle-double-up';
    const _ANGLE_DOWN = 'angle-down';
    const _ANGLE_LEFT = 'angle-left';
    const _ANGLE_RIGHT = 'angle-right';
    const _ANGLE_UP = 'angle-up';
    const _APPLE = 'apple';
    const _ARCHIVE = 'archive';
    const _AREA_CHART = 'area-chart';
    const _ARROW_CIRCLE_DOWN = 'arrow-circle-down';
    const _ARROW_CIRCLE_LEFT = 'arrow-circle-left';
    const _ARROW_CIRCLE_O_DOWN = 'arrow-circle-o-down';
    const _ARROW_CIRCLE_O_LEFT = 'arrow-circle-o-left';
    const _ARROW_CIRCLE_O_RIGHT = 'arrow-circle-o-right';
    const _ARROW_CIRCLE_O_UP = 'arrow-circle-o-up';
    const _ARROW_CIRCLE_RIGHT = 'arrow-circle-right';
    const _ARROW_CIRCLE_UP = 'arrow-circle-up';
    const _ARROW_DOWN = 'arrow-down';
    const _ARROW_LEFT = 'arrow-left';
    const _ARROW_RIGHT = 'arrow-right';
    const _ARROW_UP = 'arrow-up';
    const _ARROWS = 'arrows';
    const _ARROWS_ALT = 'arrows-alt';
    const _ARROWS_H = 'arrows-h';
    const _ARROWS_V = 'arrows-v';
    const _ASTERISK = 'asterisk';
    const _AT = 'at';
    const _AUTOMOBILE = 'automobile';
    const _BACKWARD = 'backward';
    const _BALANCE_SCALE = 'balance-scale';
    const _BAN = 'ban';
    const _BANK = 'bank';
    const _BAR_CHART = 'bar-chart';
    const _BAR_CHART_O = 'bar-chart-o';
    const _BARCODE = 'barcode';
    const _BARS = 'bars';
    const _BATTERY_0 = 'battery-0';
    const _BATTERY_1 = 'battery-1';
    const _BATTERY_2 = 'battery-2';
    const _BATTERY_3 = 'battery-3';
    const _BATTERY_4 = 'battery-4';
    const _BATTERY_EMPTY = 'battery-empty';
    const _BATTERY_FULL = 'battery-full';
    const _BATTERY_HALF = 'battery-half';
    const _BATTERY_QUARTER = 'battery-quarter';
    const _BATTERY_THREE_QUARTERS = 'battery-three-quarters';
    const _BED = 'bed';
    const _BEER = 'beer';
    const _BEHANCE = 'behance';
    const _BEHANCE_SQUARE = 'behance-square';
    const _BELL = 'bell';
    const _BELL_O = 'bell-o';
    const _BELL_SLASH = 'bell-slash';
    const _BELL_SLASH_O = 'bell-slash-o';
    const _BICYCLE = 'bicycle';
    const _BINOCULARS = 'binoculars';
    const _BIRTHDAY_CAKE = 'birthday-cake';
    const _BITBUCKET = 'bitbucket';
    const _BITBUCKET_SQUARE = 'bitbucket-square';
    const _BITCOIN = 'bitcoin';
    const _BLACK_TIE = 'black-tie';
    const _BOLD = 'bold';
    const _BOLT = 'bolt';
    const _BOMB = 'bomb';
    const _BOOK = 'book';
    const _BOOKMARK = 'bookmark';
    const _BOOKMARK_O = 'bookmark-o';
    const _BRIEFCASE = 'briefcase';
    const _BTC = 'btc';
    const _BUG = 'bug';
    const _BUILDING = 'building';
    const _BUILDING_O = 'building-o';
    const _BULLHORN = 'bullhorn';
    const _BULLSEYE = 'bullseye';
    const _BUS = 'bus';
    const _BUYSELLADS = 'buysellads';
    const _CAB = 'cab';
    const _CALCULATOR = 'calculator';
    const _CALENDAR = 'calendar';
    const _CALENDAR_CHECK_O = 'calendar-check-o';
    const _CALENDAR_MINUS_O = 'calendar-minus-o';
    const _CALENDAR_O = 'calendar-o';
    const _CALENDAR_PLUS_O = 'calendar-plus-o';
    const _CALENDAR_TIMES_O = 'calendar-times-o';
    const _CAMERA = 'camera';
    const _CAMERA_RETRO = 'camera-retro';
    const _CAR = 'car';
    const _CARET_DOWN = 'caret-down';
    const _CARET_LEFT = 'caret-left';
    const _CARET_RIGHT = 'caret-right';
    const _CARET_SQUARE_O_DOWN = 'caret-square-o-down';
    const _CARET_SQUARE_O_LEFT = 'caret-square-o-left';
    const _CARET_SQUARE_O_RIGHT = 'caret-square-o-right';
    const _CARET_SQUARE_O_UP = 'caret-square-o-up';
    const _CARET_UP = 'caret-up';
    const _CART_ARROW_DOWN = 'cart-arrow-down';
    const _CART_PLUS = 'cart-plus';
    const _CC = 'cc';
    const _CC_AMEX = 'cc-amex';
    const _CC_DINERS_CLUB = 'cc-diners-club';
    const _CC_DISCOVER = 'cc-discover';
    const _CC_JCB = 'cc-jcb';
    const _CC_MASTERCARD = 'cc-mastercard';
    const _CC_PAYPAL = 'cc-paypal';
    const _CC_STRIPE = 'cc-stripe';
    const _CC_VISA = 'cc-visa';
    const _CERTIFICATE = 'certificate';
    const _CHAIN = 'chain';
    const _CHAIN_BROKEN = 'chain-broken';
    const _CHECK = 'check';
    const _CHECK_CIRCLE = 'check-circle';
    const _CHECK_CIRCLE_O = 'check-circle-o';
    const _CHECK_SQUARE = 'check-square';
    const _CHECK_SQUARE_O = 'check-square-o';
    const _CHEVRON_CIRCLE_DOWN = 'chevron-circle-down';
    const _CHEVRON_CIRCLE_LEFT = 'chevron-circle-left';
    const _CHEVRON_CIRCLE_RIGHT = 'chevron-circle-right';
    const _CHEVRON_CIRCLE_UP = 'chevron-circle-up';
    const _CHEVRON_DOWN = 'chevron-down';
    const _CHEVRON_LEFT = 'chevron-left';
    const _CHEVRON_RIGHT = 'chevron-right';
    const _CHEVRON_UP = 'chevron-up';
    const _CHILD = 'child';
    const _CHROME = 'chrome';
    const _CIRCLE = 'circle';
    const _CIRCLE_O = 'circle-o';
    const _CIRCLE_O_NOTCH = 'circle-o-notch';
    const _CIRCLE_THIN = 'circle-thin';
    const _CLIPBOARD = 'clipboard';
    const _CLOCK_O = 'clock-o';
    const _CLONE = 'clone';
    const _CLOSE = 'close';
    const _CLOUD = 'cloud';
    const _CLOUD_DOWNLOAD = 'cloud-download';
    const _CLOUD_UPLOAD = 'cloud-upload';
    const _CNY = 'cny';
    const _CODE = 'code';
    const _CODE_FORK = 'code-fork';
    const _CODEPEN = 'codepen';
    const _COFFEE = 'coffee';
    const _COG = 'cog';
    const _COGS = 'cogs';
    const _COLUMNS = 'columns';
    const _COMMENT = 'comment';
    const _COMMENT_O = 'comment-o';
    const _COMMENTING = 'commenting';
    const _COMMENTING_O = 'commenting-o';
    const _COMMENTS = 'comments';
    const _COMMENTS_O = 'comments-o';
    const _COMPASS = 'compass';
    const _COMPRESS = 'compress';
    const _CONNECTDEVELOP = 'connectdevelop';
    const _CONTAO = 'contao';
    const _COPY = 'copy';
    const _COPYRIGHT = 'copyright';
    const _CREATIVE_COMMONS = 'creative-commons';
    const _CREDIT_CARD = 'credit-card';
    const _CROP = 'crop';
    const _CROSSHAIRS = 'crosshairs';
    const _CSS3 = 'css3';
    const _CUBE = 'cube';
    const _CUBES = 'cubes';
    const _CUT = 'cut';
    const _CUTLERY = 'cutlery';
    const _DASHBOARD = 'dashboard';
    const _DASHCUBE = 'dashcube';
    const _DATABASE = 'database';
    const _DEDENT = 'dedent';
    const _DELICIOUS = 'delicious';
    const _DESKTOP = 'desktop';
    const _DEVIANTART = 'deviantart';
    const _DIAMOND = 'diamond';
    const _DIGG = 'digg';
    const _DOLLAR = 'dollar';
    const _DOT_CIRCLE_O = 'dot-circle-o';
    const _DOWNLOAD = 'download';
    const _DRIBBBLE = 'dribbble';
    const _DROPBOX = 'dropbox';
    const _DRUPAL = 'drupal';
    const _EDIT = 'edit';
    const _EJECT = 'eject';
    const _ELLIPSIS_H = 'ellipsis-h';
    const _ELLIPSIS_V = 'ellipsis-v';
    const _EMPIRE = 'empire';
    const _ENVELOPE = 'envelope';
    const _ENVELOPE_O = 'envelope-o';
    const _ENVELOPE_SQUARE = 'envelope-square';
    const _ERASER = 'eraser';
    const _EUR = 'eur';
    const _EURO = 'euro';
    const _EXCHANGE = 'exchange';
    const _EXCLAMATION = 'exclamation';
    const _EXCLAMATION_CIRCLE = 'exclamation-circle';
    const _EXCLAMATION_TRIANGLE = 'exclamation-triangle';
    const _EXPAND = 'expand';
    const _EXPEDITEDSSL = 'expeditedssl';
    const _EXTERNAL_LINK = 'external-link';
    const _EXTERNAL_LINK_SQUARE = 'external-link-square';
    const _EYE = 'eye';
    const _EYE_SLASH = 'eye-slash';
    const _EYEDROPPER = 'eyedropper';
    const _FACEBOOK = 'facebook';
    const _FACEBOOK_F = 'facebook-f';
    const _FACEBOOK_OFFICIAL = 'facebook-official';
    const _FACEBOOK_SQUARE = 'facebook-square';
    const _FAST_BACKWARD = 'fast-backward';
    const _FAST_FORWARD = 'fast-forward';
    const _FAX = 'fax';
    const _FEED = 'feed';
    const _FEMALE = 'female';
    const _FIGHTER_JET = 'fighter-jet';
    const _FILE = 'file';
    const _FILE_ARCHIVE_O = 'file-archive-o';
    const _FILE_AUDIO_O = 'file-audio-o';
    const _FILE_CODE_O = 'file-code-o';
    const _FILE_EXCEL_O = 'file-excel-o';
    const _FILE_IMAGE_O = 'file-image-o';
    const _FILE_MOVIE_O = 'file-movie-o';
    const _FILE_O = 'file-o';
    const _FILE_PDF_O = 'file-pdf-o';
    const _FILE_PHOTO_O = 'file-photo-o';
    const _FILE_PICTURE_O = 'file-picture-o';
    const _FILE_POWERPOINT_O = 'file-powerpoint-o';
    const _FILE_SOUND_O = 'file-sound-o';
    const _FILE_TEXT = 'file-text';
    const _FILE_TEXT_O = 'file-text-o';
    const _FILE_VIDEO_O = 'file-video-o';
    const _FILE_WORD_O = 'file-word-o';
    const _FILE_ZIP_O = 'file-zip-o';
    const _FILES_O = 'files-o';
    const _FILM = 'film';
    const _FILTER = 'filter';
    const _FIRE = 'fire';
    const _FIRE_EXTINGUISHER = 'fire-extinguisher';
    const _FIREFOX = 'firefox';
    const _FLAG = 'flag';
    const _FLAG_CHECKERED = 'flag-checkered';
    const _FLAG_O = 'flag-o';
    const _FLASH = 'flash';
    const _FLASK = 'flask';
    const _FLICKR = 'flickr';
    const _FLOPPY_O = 'floppy-o';
    const _FOLDER = 'folder';
    const _FOLDER_O = 'folder-o';
    const _FOLDER_OPEN = 'folder-open';
    const _FOLDER_OPEN_O = 'folder-open-o';
    const _FONT = 'font';
    const _FONTICONS = 'fonticons';
    const _FORUMBEE = 'forumbee';
    const _FORWARD = 'forward';
    const _FOURSQUARE = 'foursquare';
    const _FROWN_O = 'frown-o';
    const _FUTBOL_O = 'futbol-o';
    const _GAMEPAD = 'gamepad';
    const _GAVEL = 'gavel';
    const _GBP = 'gbp';
    const _GE = 'ge';
    const _GEAR = 'gear';
    const _GEARS = 'gears';
    const _GENDERLESS = 'genderless';
    const _GET_POCKET = 'get-pocket';
    const _GG = 'gg';
    const _GG_CIRCLE = 'gg-circle';
    const _GIFT = 'gift';
    const _GIT = 'git';
    const _GIT_SQUARE = 'git-square';
    const _GITHUB = 'github';
    const _GITHUB_ALT = 'github-alt';
    const _GITHUB_SQUARE = 'github-square';
    const _GITTIP = 'gittip';
    const _GLASS = 'glass';
    const _GLOBE = 'globe';
    const _GOOGLE = 'google';
    const _GOOGLE_PLUS = 'google-plus';
    const _GOOGLE_PLUS_SQUARE = 'google-plus-square';
    const _GOOGLE_WALLET = 'google-wallet';
    const _GRADUATION_CAP = 'graduation-cap';
    const _GRATIPAY = 'gratipay';
    const _GROUP = 'group';
    const _H_SQUARE = 'h-square';
    const _HACKER_NEWS = 'hacker-news';
    const _HAND_GRAB_O = 'hand-grab-o';
    const _HAND_LIZARD_O = 'hand-lizard-o';
    const _HAND_O_DOWN = 'hand-o-down';
    const _HAND_O_LEFT = 'hand-o-left';
    const _HAND_O_RIGHT = 'hand-o-right';
    const _HAND_O_UP = 'hand-o-up';
    const _HAND_PAPER_O = 'hand-paper-o';
    const _HAND_PEACE_O = 'hand-peace-o';
    const _HAND_POINTER_O = 'hand-pointer-o';
    const _HAND_ROCK_O = 'hand-rock-o';
    const _HAND_SCISSORS_O = 'hand-scissors-o';
    const _HAND_SPOCK_O = 'hand-spock-o';
    const _HAND_STOP_O = 'hand-stop-o';
    const _HDD_O = 'hdd-o';
    const _HEADER = 'header';
    const _HEADPHONES = 'headphones';
    const _HEART = 'heart';
    const _HEART_O = 'heart-o';
    const _HEARTBEAT = 'heartbeat';
    const _HISTORY = 'history';
    const _HOME = 'home';
    const _HOSPITAL_O = 'hospital-o';
    const _HOTEL = 'hotel';
    const _HOURGLASS = 'hourglass';
    const _HOURGLASS_1 = 'hourglass-1';
    const _HOURGLASS_2 = 'hourglass-2';
    const _HOURGLASS_3 = 'hourglass-3';
    const _HOURGLASS_END = 'hourglass-end';
    const _HOURGLASS_HALF = 'hourglass-half';
    const _HOURGLASS_O = 'hourglass-o';
    const _HOURGLASS_START = 'hourglass-start';
    const _HOUZZ = 'houzz';
    const _HTML5 = 'html5';
    const _I_CURSOR = 'i-cursor';
    const _ILS = 'ils';
    const _IMAGE = 'image';
    const _INBOX = 'inbox';
    const _INDENT = 'indent';
    const _INDUSTRY = 'industry';
    const _INFO = 'info';
    const _INFO_CIRCLE = 'info-circle';
    const _INR = 'inr';
    const _INSTAGRAM = 'instagram';
    const _INSTITUTION = 'institution';
    const _INTERNET_EXPLORER = 'internet-explorer';
    const _INTERSEX = 'intersex';
    const _IOXHOST = 'ioxhost';
    const _ITALIC = 'italic';
    const _JOOMLA = 'joomla';
    const _JPY = 'jpy';
    const _JSFIDDLE = 'jsfiddle';
    const _KEY = 'key';
    const _KEYBOARD_O = 'keyboard-o';
    const _KRW = 'krw';
    const _LANGUAGE = 'language';
    const _LAPTOP = 'laptop';
    const _LASTFM = 'lastfm';
    const _LASTFM_SQUARE = 'lastfm-square';
    const _LEAF = 'leaf';
    const _LEANPUB = 'leanpub';
    const _LEGAL = 'legal';
    const _LEMON_O = 'lemon-o';
    const _LEVEL_DOWN = 'level-down';
    const _LEVEL_UP = 'level-up';
    const _LIFE_BOUY = 'life-bouy';
    const _LIFE_BUOY = 'life-buoy';
    const _LIFE_RING = 'life-ring';
    const _LIFE_SAVER = 'life-saver';
    const _LIGHTBULB_O = 'lightbulb-o';
    const _LINE_CHART = 'line-chart';
    const _LINK = 'link';
    const _LINKEDIN = 'linkedin';
    const _LINKEDIN_SQUARE = 'linkedin-square';
    const _LINUX = 'linux';
    const _LIST = 'list';
    const _LIST_ALT = 'list-alt';
    const _LIST_OL = 'list-ol';
    const _LIST_UL = 'list-ul';
    const _LOCATION_ARROW = 'location-arrow';
    const _LOCK = 'lock';
    const _LONG_ARROW_DOWN = 'long-arrow-down';
    const _LONG_ARROW_LEFT = 'long-arrow-left';
    const _LONG_ARROW_RIGHT = 'long-arrow-right';
    const _LONG_ARROW_UP = 'long-arrow-up';
    const _MAGIC = 'magic';
    const _MAGNET = 'magnet';
    const _MAIL_FORWARD = 'mail-forward';
    const _MAIL_REPLY = 'mail-reply';
    const _MAIL_REPLY_ALL = 'mail-reply-all';
    const _MALE = 'male';
    const _MAP = 'map';
    const _MAP_MARKER = 'map-marker';
    const _MAP_O = 'map-o';
    const _MAP_PIN = 'map-pin';
    const _MAP_SIGNS = 'map-signs';
    const _MARS = 'mars';
    const _MARS_DOUBLE = 'mars-double';
    const _MARS_STROKE = 'mars-stroke';
    const _MARS_STROKE_H = 'mars-stroke-h';
    const _MARS_STROKE_V = 'mars-stroke-v';
    const _MAXCDN = 'maxcdn';
    const _MEANPATH = 'meanpath';
    const _MEDIUM = 'medium';
    const _MEDKIT = 'medkit';
    const _MEH_O = 'meh-o';
    const _MERCURY = 'mercury';
    const _MICROPHONE = 'microphone';
    const _MICROPHONE_SLASH = 'microphone-slash';
    const _MINUS = 'minus';
    const _MINUS_CIRCLE = 'minus-circle';
    const _MINUS_SQUARE = 'minus-square';
    const _MINUS_SQUARE_O = 'minus-square-o';
    const _MOBILE = 'mobile';
    const _MOBILE_PHONE = 'mobile-phone';
    const _MONEY = 'money';
    const _MOON_O = 'moon-o';
    const _MORTAR_BOARD = 'mortar-board';
    const _MOTORCYCLE = 'motorcycle';
    const _MOUSE_POINTER = 'mouse-pointer';
    const _MUSIC = 'music';
    const _NAVICON = 'navicon';
    const _NEUTER = 'neuter';
    const _NEWSPAPER_O = 'newspaper-o';
    const _OBJECT_GROUP = 'object-group';
    const _OBJECT_UNGROUP = 'object-ungroup';
    const _ODNOKLASSNIKI = 'odnoklassniki';
    const _ODNOKLASSNIKI_SQUARE = 'odnoklassniki-square';
    const _OPENCART = 'opencart';
    const _OPENID = 'openid';
    const _OPERA = 'opera';
    const _OPTIN_MONSTER = 'optin-monster';
    const _OUTDENT = 'outdent';
    const _PAGELINES = 'pagelines';
    const _PAINT_BRUSH = 'paint-brush';
    const _PAPER_PLANE = 'paper-plane';
    const _PAPER_PLANE_O = 'paper-plane-o';
    const _PAPERCLIP = 'paperclip';
    const _PARAGRAPH = 'paragraph';
    const _PASTE = 'paste';
    const _PAUSE = 'pause';
    const _PAW = 'paw';
    const _PAYPAL = 'paypal';
    const _PENCIL = 'pencil';
    const _PENCIL_SQUARE = 'pencil-square';
    const _PENCIL_SQUARE_O = 'pencil-square-o';
    const _PHONE = 'phone';
    const _PHONE_SQUARE = 'phone-square';
    const _PHOTO = 'photo';
    const _PICTURE_O = 'picture-o';
    const _PIE_CHART = 'pie-chart';
    const _PIED_PIPER = 'pied-piper';
    const _PIED_PIPER_ALT = 'pied-piper-alt';
    const _PINTEREST = 'pinterest';
    const _PINTEREST_P = 'pinterest-p';
    const _PINTEREST_SQUARE = 'pinterest-square';
    const _PLANE = 'plane';
    const _PLAY = 'play';
    const _PLAY_CIRCLE = 'play-circle';
    const _PLAY_CIRCLE_O = 'play-circle-o';
    const _PLUG = 'plug';
    const _PLUS = 'plus';
    const _PLUS_CIRCLE = 'plus-circle';
    const _PLUS_SQUARE = 'plus-square';
    const _PLUS_SQUARE_O = 'plus-square-o';
    const _POWER_OFF = 'power-off';
    const _PRINT = 'print';
    const _PUZZLE_PIECE = 'puzzle-piece';
    const _QQ = 'qq';
    const _QRCODE = 'qrcode';
    const _QUESTION = 'question';
    const _QUESTION_CIRCLE = 'question-circle';
    const _QUOTE_LEFT = 'quote-left';
    const _QUOTE_RIGHT = 'quote-right';
    const _RA = 'ra';
    const _RANDOM = 'random';
    const _REBEL = 'rebel';
    const _RECYCLE = 'recycle';
    const _REDDIT = 'reddit';
    const _REDDIT_SQUARE = 'reddit-square';
    const _REFRESH = 'refresh';
    const _REGISTERED = 'registered';
    const _REMOVE = 'remove';
    const _RENREN = 'renren';
    const _REORDER = 'reorder';
    const _REPEAT = 'repeat';
    const _REPLY = 'reply';
    const _REPLY_ALL = 'reply-all';
    const _RETWEET = 'retweet';
    const _RMB = 'rmb';
    const _ROAD = 'road';
    const _ROCKET = 'rocket';
    const _ROTATE_LEFT = 'rotate-left';
    const _ROTATE_RIGHT = 'rotate-right';
    const _ROUBLE = 'rouble';
    const _RSS = 'rss';
    const _RSS_SQUARE = 'rss-square';
    const _RUB = 'rub';
    const _RUBLE = 'ruble';
    const _RUPEE = 'rupee';
    const _SAFARI = 'safari';
    const _SAVE = 'save';
    const _SCISSORS = 'scissors';
    const _SEARCH = 'search';
    const _SEARCH_MINUS = 'search-minus';
    const _SEARCH_PLUS = 'search-plus';
    const _SELLSY = 'sellsy';
    const _SEND = 'send';
    const _SEND_O = 'send-o';
    const _SERVER = 'server';
    const _SHARE = 'share';
    const _SHARE_ALT = 'share-alt';
    const _SHARE_ALT_SQUARE = 'share-alt-square';
    const _SHARE_SQUARE = 'share-square';
    const _SHARE_SQUARE_O = 'share-square-o';
    const _SHEKEL = 'shekel';
    const _SHEQEL = 'sheqel';
    const _SHIELD = 'shield';
    const _SHIP = 'ship';
    const _SHIRTSINBULK = 'shirtsinbulk';
    const _SHOPPING_CART = 'shopping-cart';
    const _SIGN_IN = 'sign-in';
    const _SIGN_OUT = 'sign-out';
    const _SIGNAL = 'signal';
    const _SIMPLYBUILT = 'simplybuilt';
    const _SITEMAP = 'sitemap';
    const _SKYATLAS = 'skyatlas';
    const _SKYPE = 'skype';
    const _SLACK = 'slack';
    const _SLIDERS = 'sliders';
    const _SLIDESHARE = 'slideshare';
    const _SMILE_O = 'smile-o';
    const _SOCCER_BALL_O = 'soccer-ball-o';
    const _SORT = 'sort';
    const _SORT_ALPHA_ASC = 'sort-alpha-asc';
    const _SORT_ALPHA_DESC = 'sort-alpha-desc';
    const _SORT_AMOUNT_ASC = 'sort-amount-asc';
    const _SORT_AMOUNT_DESC = 'sort-amount-desc';
    const _SORT_ASC = 'sort-asc';
    const _SORT_DESC = 'sort-desc';
    const _SORT_DOWN = 'sort-down';
    const _SORT_NUMERIC_ASC = 'sort-numeric-asc';
    const _SORT_NUMERIC_DESC = 'sort-numeric-desc';
    const _SORT_UP = 'sort-up';
    const _SOUNDCLOUD = 'soundcloud';
    const _SPACE_SHUTTLE = 'space-shuttle';
    const _SPINNER = 'spinner';
    const _SPOON = 'spoon';
    const _SPOTIFY = 'spotify';
    const _SQUARE = 'square';
    const _SQUARE_O = 'square-o';
    const _STACK_EXCHANGE = 'stack-exchange';
    const _STACK_OVERFLOW = 'stack-overflow';
    const _STAR = 'star';
    const _STAR_HALF = 'star-half';
    const _STAR_HALF_EMPTY = 'star-half-empty';
    const _STAR_HALF_FULL = 'star-half-full';
    const _STAR_HALF_O = 'star-half-o';
    const _STAR_O = 'star-o';
    const _STEAM = 'steam';
    const _STEAM_SQUARE = 'steam-square';
    const _STEP_BACKWARD = 'step-backward';
    const _STEP_FORWARD = 'step-forward';
    const _STETHOSCOPE = 'stethoscope';
    const _STICKY_NOTE = 'sticky-note';
    const _STICKY_NOTE_O = 'sticky-note-o';
    const _STOP = 'stop';
    const _STREET_VIEW = 'street-view';
    const _STRIKETHROUGH = 'strikethrough';
    const _STUMBLEUPON = 'stumbleupon';
    const _STUMBLEUPON_CIRCLE = 'stumbleupon-circle';
    const _SUBSCRIPT = 'subscript';
    const _SUBWAY = 'subway';
    const _SUITCASE = 'suitcase';
    const _SUN_O = 'sun-o';
    const _SUPERSCRIPT = 'superscript';
    const _SUPPORT = 'support';
    const _TABLE = 'table';
    const _TABLET = 'tablet';
    const _TACHOMETER = 'tachometer';
    const _TAG = 'tag';
    const _TAGS = 'tags';
    const _TASKS = 'tasks';
    const _TAXI = 'taxi';
    const _TELEVISION = 'television';
    const _TENCENT_WEIBO = 'tencent-weibo';
    const _TERMINAL = 'terminal';
    const _TEXT_HEIGHT = 'text-height';
    const _TEXT_WIDTH = 'text-width';
    const _TH = 'th';
    const _TH_LARGE = 'th-large';
    const _TH_LIST = 'th-list';
    const _THUMB_TACK = 'thumb-tack';
    const _THUMBS_DOWN = 'thumbs-down';
    const _THUMBS_O_DOWN = 'thumbs-o-down';
    const _THUMBS_O_UP = 'thumbs-o-up';
    const _THUMBS_UP = 'thumbs-up';
    const _TICKET = 'ticket';
    const _TIMES = 'times';
    const _TIMES_CIRCLE = 'times-circle';
    const _TIMES_CIRCLE_O = 'times-circle-o';
    const _TINT = 'tint';
    const _TOGGLE_DOWN = 'toggle-down';
    const _TOGGLE_LEFT = 'toggle-left';
    const _TOGGLE_OFF = 'toggle-off';
    const _TOGGLE_ON = 'toggle-on';
    const _TOGGLE_RIGHT = 'toggle-right';
    const _TOGGLE_UP = 'toggle-up';
    const _TRADEMARK = 'trademark';
    const _TRAIN = 'train';
    const _TRANSGENDER = 'transgender';
    const _TRANSGENDER_ALT = 'transgender-alt';
    const _TRASH = 'trash';
    const _TRASH_O = 'trash-o';
    const _TREE = 'tree';
    const _TRELLO = 'trello';
    const _TRIPADVISOR = 'tripadvisor';
    const _TROPHY = 'trophy';
    const _TRUCK = 'truck';
    const _TRY = 'try';
    const _TTY = 'tty';
    const _TUMBLR = 'tumblr';
    const _TUMBLR_SQUARE = 'tumblr-square';
    const _TURKISH_LIRA = 'turkish-lira';
    const _TV = 'tv';
    const _TWITCH = 'twitch';
    const _TWITTER = 'twitter';
    const _TWITTER_SQUARE = 'twitter-square';
    const _UMBRELLA = 'umbrella';
    const _UNDERLINE = 'underline';
    const _UNDO = 'undo';
    const _UNIVERSITY = 'university';
    const _UNLINK = 'unlink';
    const _UNLOCK = 'unlock';
    const _UNLOCK_ALT = 'unlock-alt';
    const _UNSORTED = 'unsorted';
    const _UPLOAD = 'upload';
    const _USD = 'usd';
    const _USER = 'user';
    const _USER_MD = 'user-md';
    const _USER_PLUS = 'user-plus';
    const _USER_SECRET = 'user-secret';
    const _USER_TIMES = 'user-times';
    const _USERS = 'users';
    const _VENUS = 'venus';
    const _VENUS_DOUBLE = 'venus-double';
    const _VENUS_MARS = 'venus-mars';
    const _VIACOIN = 'viacoin';
    const _VIDEO_CAMERA = 'video-camera';
    const _VIMEO = 'vimeo';
    const _VIMEO_SQUARE = 'vimeo-square';
    const _VINE = 'vine';
    const _VK = 'vk';
    const _VOLUME_DOWN = 'volume-down';
    const _VOLUME_OFF = 'volume-off';
    const _VOLUME_UP = 'volume-up';
    const _WARNING = 'warning';
    const _WECHAT = 'wechat';
    const _WEIBO = 'weibo';
    const _WEIXIN = 'weixin';
    const _WHATSAPP = 'whatsapp';
    const _WHEELCHAIR = 'wheelchair';
    const _WIFI = 'wifi';
    const _WIKIPEDIA_W = 'wikipedia-w';
    const _WINDOWS = 'windows';
    const _WON = 'won';
    const _WORDPRESS = 'wordpress';
    const _WRENCH = 'wrench';
    const _XING = 'xing';
    const _XING_SQUARE = 'xing-square';
    const _Y_COMBINATOR = 'y-combinator';
    const _Y_COMBINATOR_SQUARE = 'y-combinator-square';
    const _YAHOO = 'yahoo';
    const _YC = 'yc';
    const _YC_SQUARE = 'yc-square';
    const _YELP = 'yelp';
    const _YEN = 'yen';
    const _YOUTUBE = 'youtube';
    const _YOUTUBE_PLAY = 'youtube-play';
    const _YOUTUBE_SQUARE = 'youtube-square';
}