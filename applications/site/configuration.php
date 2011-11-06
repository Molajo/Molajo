<?php
class MolajoConfigApplication {

	/* Cache Settings */
	public $caching = '0';
	public $cachetime = '15';
	public $cache_handler = 'file';

	/* Meta Settings */
	public $MetaDesc = 'Molajo - the Cats Meow';
	public $MetaKeys = 'molajo, Molajo';
	public $MetaAuthor = '1';

	/* SEO Settings */
	public $sef = '1';
	public $sef_rewrite = '0';
	public $sef_suffix = '0';
	public $unicodeslugs = '0';

    /* Application Access */
    public $application_logon_requirement = '1';
    public $application_guest_option = 'com_login';
    public $application_default_option = 'com_dashboard';
    public $default_template_extension_id = 209;
}
