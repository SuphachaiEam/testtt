<?php
// created: 2015-08-26 11:27:58
$sugar_config = array (
  'appname' => 'citipa-test',
  'admin_access_control' => false,
  'admin_export_only' => false,
  'cache_dir' => 'cache/',
  'calculate_response_time' => true,
  'common_ml_dir' => '',
  'create_default_user' => false,
  'currency' => '',
  'dashlet_display_row_options' => 
  array (
    0 => '1',
    1 => '3',
    2 => '5',
    3 => '10',
  ),
  'date_formats' => 
  array (
    'Y-m-d' => '2006-12-23',
    'm-d-Y' => '12-23-2006',
    'd-m-Y' => '23-12-2006',
    'Y/m/d' => '2006/12/23',
    'm/d/Y' => '12/23/2006',
    'd/m/Y' => '23/12/2006',
    'Y.m.d' => '2006.12.23',
    'd.m.Y' => '23.12.2006',
    'm.d.Y' => '12.23.2006',
  ),
  'datef' => 'd/m/Y',
  'dbconfig' => 
  array (
    'db_host_name' => 'localhost',
    'db_host_instance' => '',
    'db_user_name' => 'citipa_dev',
    'db_password' => 'citipa_dev',
    'db_name' => '',
    'db_type' => 'mssql',
  ),
  'dbconfigoption' => 
  array (
    'persistent' => false,
    'autofree' => false,
    'debug' => 0,
    'seqname_format' => '%s_seq',
    'portability' => 0,
    'ssl' => false,
  ),
  'default_action' => 'index',
  'default_charset' => 'UTF-8',
  'default_currencies' => 
  array (
    'AUD' => 
    array (
      'name' => 'Australian Dollars',
      'iso4217' => 'AUD',
      'symbol' => '$',
    ),
    'BRL' => 
    array (
      'name' => 'Brazilian Reais',
      'iso4217' => 'BRL',
      'symbol' => 'R$',
    ),
    'GBP' => 
    array (
      'name' => 'British Pounds',
      'iso4217' => 'GBP',
      'symbol' => 'ยฃ',
    ),
    'CAD' => 
    array (
      'name' => 'Canadian Dollars',
      'iso4217' => 'CAD',
      'symbol' => '$',
    ),
    'CNY' => 
    array (
      'name' => 'Chinese Yuan',
      'iso4217' => 'CNY',
      'symbol' => '๏ฟฅ',
    ),
    'EUR' => 
    array (
      'name' => 'Euro',
      'iso4217' => 'EUR',
      'symbol' => 'โ�ฌ',
    ),
    'HKD' => 
    array (
      'name' => 'Hong Kong Dollars',
      'iso4217' => 'HKD',
      'symbol' => '$',
    ),
    'INR' => 
    array (
      'name' => 'Indian Rupees',
      'iso4217' => 'INR',
      'symbol' => 'โ�จ',
    ),
    'KRW' => 
    array (
      'name' => 'Korean Won',
      'iso4217' => 'KRW',
      'symbol' => 'โ�ฉ',
    ),
    'YEN' => 
    array (
      'name' => 'Japanese Yen',
      'iso4217' => 'JPY',
      'symbol' => 'ยฅ',
    ),
    'MXM' => 
    array (
      'name' => 'Mexican Pesos',
      'iso4217' => 'MXM',
      'symbol' => '$',
    ),
    'SGD' => 
    array (
      'name' => 'Singaporean Dollars',
      'iso4217' => 'SGD',
      'symbol' => '$',
    ),
    'CHF' => 
    array (
      'name' => 'Swiss Franc',
      'iso4217' => 'CHF',
      'symbol' => 'SFr.',
    ),
    'THB' => 
    array (
      'name' => 'Thai Baht',
      'iso4217' => 'THB',
      'symbol' => 'เธฟ',
    ),
    'USD' => 
    array (
      'name' => 'US Dollars',
      'iso4217' => 'USD',
      'symbol' => '$',
    ),
  ),
  'default_currency_iso4217' => 'THB',
  'default_currency_name' => 'Thai Baht',
  'default_currency_significant_digits' => '2',
  'default_currency_symbol' => 'เธฟ',
  'default_date_format' => 'd/m/Y',
  'default_decimal_seperator' => '.',
  'default_email_charset' => 'UTF-8',
  'default_email_client' => 'sugar',
  'default_email_editor' => 'html',
  'default_export_charset' => 'UTF-8',
  'default_language' => 'en_us',
  'default_locale_name_format' => 's f l',
  'default_max_subtabs' => '12',
  'default_max_tabs' => '12',
  'default_module' => 'Home',
  'default_navigation_paradigm' => 'm',
  'default_number_grouping_seperator' => ',',
  'default_password' => '',
  'default_permissions' => 
  array (
    'dir_mode' => 1528,
    'file_mode' => 432,
    'user' => '',
    'group' => '',
  ),
  'default_subpanel_links' => false,
  'default_subpanel_tabs' => true,
  'default_swap_last_viewed' => false,
  'default_swap_shortcuts' => false,
  'default_theme' => 'Sugar',
  'default_time_format' => 'H:i',
  'default_user_is_admin' => false,
  'default_user_name' => '',
  'disable_export' => false,
  'disable_persistent_connections' => 'false',
  'display_email_template_variable_chooser' => false,
  'display_inbound_email_buttons' => false,
  'dump_slow_queries' => true,
  'email_default_client' => 'sugar',
  'email_default_delete_attachments' => true,
  'email_default_editor' => 'html',
  'email_num_autoreplies_24_hours' => 10,
  'export_delimiter' => ',',
  'history_max_viewed' => 10,
  'host_name' => 'localhost',
  'i18n_test' => false,
  'import_dir' => 'cache/import/',
  'import_max_execution_time' => 60000,
  'import_max_records_per_file' => '10000',
  'installer_locked' => true,
  'js_custom_version' => 1,
  'js_lang_version' => 3,
  'languages' => 
  array (
    'en_us' => 'English (US)',
  ),
  'large_scale_test' => false,
  'list_max_entries_per_page' => '50',
  'list_max_entries_per_subpanel' => 10,
  'lock_default_user_name' => false,
  'lock_homepage' => false,
  'lock_subpanels' => false,
  'log_dir' => '.',
  'log_file' => 'sugarcrm.log',
  'log_memory_usage' => false,
  'logger' => 
  array (
    'level' => 'error',
    'file' => 
    array (
      'ext' => '.log',
      'name' => 'sugarcrm',
      'dateFormat' => '%c',
      'maxSize' => '10MB',
      'maxLogs' => 10,
      'suffix' => '%m_%Y',
    ),
  ),
  'login_nav' => false,
  'max_dashlets_homepage' => '15',
  'portal_view' => 'single_user',
  'require_accounts' => true,
  'resource_management' => 
  array (
    'special_query_limit' => 500000,
    'special_query_modules' => 
    array (
      0 => 'Reports',
      1 => 'Export',
      2 => 'Import',
      3 => 'Administration',
      4 => 'Sync',
    ),
    'default_limit' => 500000,
  ),
  'rss_cache_time' => '10800',
  'save_query' => 'all',
  'session_dir' => '',
  'showDetailData' => true,
  'showThemePicker' => true,
  'site_url' => 'http://localhost/CitiPA',
  'slow_query_time_msec' => '1000',
  'sugar_version' => '5.1.0b',
  'sugarbeet' => false,
  'time_formats' => 
  array (
    'H:i' => '23:00',
    'h:ia' => '11:00pm',
    'h:iA' => '11:00PM',
    'H.i' => '23.00',
    'h.ia' => '11.00pm',
    'h.iA' => '11.00PM',
  ),
  'timef' => 'H:i',
  'tmp_dir' => 'cache/xml/',
  'translation_string_prefix' => false,
  'unique_key' => 'e1639cdfb997f2bfb6d04d17b3fdc334',
  'upload_badext' => 
  array (
    0 => 'php',
    1 => 'php3',
    2 => 'php4',
    3 => 'php5',
    4 => 'pl',
    5 => 'cgi',
    6 => 'py',
    7 => 'asp',
    8 => 'cfm',
    9 => 'js',
    10 => 'vbs',
    11 => 'html',
    12 => 'htm',
  ),
  'upload_dir' => 'cache/upload/',
  'upload_maxsize' => 3000000,
  'use_common_ml_dir' => false,
  'use_php_code_json' => true,
  'verify_client_ip' => false,
  'enhanced_search_version' => 'enhancedSearch3-0_demo',
  'use_real_names' => false,
  'stack_trace_errors' => false,
  'developerMode' => true,
  'disable_vcr' => false,
  'hide_subpanels' => true,
  'obt_refer_campaigns' => 
  array (
    0 => 'A Refer 2013',
    1 => 'A Refer 2014',
    2 => 'A Refer 2015',
    3 => 'A Refer 2016',
  ),
  'genesys' => 
  array (
    'ftp_host' => 'g1-db-p-env1',
    'ftp_user' => 'crmuser',
    'ftp_password' => 'UEBzc3cwcmQ=',
  	'get_chain_id_seq_url' => 'http://crm-link.cut.co.th/crmcentre/getchainidsequence.php?row_count=',
  ),
  'crm-centre' => 
  array (
  	// 'url' => 'http://crm-link.cut.co.th/crm-centre/web/index.php?r={$controller}/{$action}',
  	'url' => 'http://bk0995:81/crm-centre/web/index.php?r={$controller}/{$action}',
  	//'url' => 'http://crm4-test.th.msig.com/dev/crm-centre/web/index.php?r={$controller}/{$action}',
  	'default_params' => '',
  ),
);
?>
