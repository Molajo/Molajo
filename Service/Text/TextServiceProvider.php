<?php
/**
 * Text Service Provider
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Text;

use Molajo\IoC\AbstractServiceProvider;
use CommonApi\IoC\ServiceProviderInterface;

/**
 * Text Controller Service Provider
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class TextServiceProvider extends AbstractServiceProvider implements ServiceProviderInterface
{
    /**
     * Constructor
     *
     * @param   $options
     *
     * @since   1.0
     */
    public function __construct(array $options = array())
    {
        $options['service_namespace']        = 'Molajo\\Controller\\TextController';
        $options['store_instance_indicator'] = true;
        $options['service_name']             = basename(__DIR__);

        parent::__construct($options);
    }

    /**
     * Identify Class Dependencies for Constructor Injection
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function setDependencies(array $reflection = null)
    {
        $reflection = null;

        $this->dependencies['Resource'] = array();

        return $this->dependencies;
    }

    /**
     * Instantiate Class
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function instantiateService()
    {
        $class = 'Molajo\\Controller\\TextController';

        $this->service_instance = new $class(
            $this->dependencies['Resource'],
            $this->getTextList()
        );

        return $this;
    }

    /**
     * Instantiate Class
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function getTextList()
    {
        return array(
            'lorem',
            'ipsum',
            'dolor',
            'sit',
            'amet',
            'consectetur',
            'adipisicing',
            'elit',
            'sed',
            'do',
            'eiusmod',
            'tempor',
            'incididunt',
            'ut',
            'labore',
            'etdolore',
            'magna',
            'aliqua',
            'enim',
            'ad',
            'minim',
            'veniam',
            'quis',
            'nostrud',
            'exercitation',
            'ullamco',
            'laboris',
            'nisi',
            'aliquip',
            'ex',
            'ea',
            'commodo',
            'consequatduis',
            'aute',
            'irure',
            'in',
            'reprehenderit',
            'voluptate',
            'velit',
            'esse',
            'cillum',
            'dolore',
            'eu',
            'fugiat',
            'nulla',
            'pariatur',
            'excepteur',
            'sint',
            'occaecatcupidatat',
            'non',
            'proident',
            'sunt',
            'culpa',
            'qui',
            'officia',
            'deserunt',
            'mollit',
            'anim',
            'id',
            'est',
            'laborumcurabitur',
            'pretium',
            'tincidunt',
            'lacus',
            'gravida',
            'orci',
            'a',
            'odio',
            'nullam',
            'varius',
            'turpis',
            'etcommodo',
            'pharetra',
            'eros',
            'bibendum',
            'nec',
            'luctus',
            'felis',
            'sollicitudin',
            'mauris',
            'integerin',
            'nibh',
            'euismod',
            'duis',
            'ac',
            'tellus',
            'et',
            'risus',
            'vulputate',
            'vehicula',
            'donec',
            'lobortisrisus',
            'etiam',
            'ullamcorper',
            'ligula',
            'congue',
            'turpisid',
            'sapien',
            'quam',
            'maecenas',
            'fermentum',
            'consequat',
            'mi',
            'pellentesquemalesuada',
            'sem',
            'aliquet',
            'eget',
            'neque',
            'aliquam',
            'faucibuselit',
            'dictum',
            'nisl',
            'adipiscing',
            'malesuada',
            'diam',
            'erat',
            'cras',
            'mollisscelerisque',
            'nunc',
            'arcu',
            'curabitur',
            'php',
            'augue',
            'dapibus',
            'laoreet',
            'etpretium',
            'aenean',
            'mollis',
            'molestie',
            'feugiat',
            'hac',
            'habitasse',
            'platea',
            'dictumstfusce',
            'convallis',
            'imperdiet',
            'suscipit',
            'placeratipsum',
            'urna',
            'libero',
            'tristique',
            'sodalesmauris',
            'mattis',
            'semper',
            'leo',
            'dictumst',
            'vivamus',
            'facilisis',
            'at',
            'odiomauris',
            'elementum',
            'metus',
            'nonfeugiat',
            'vitae',
            'morbi',
            'maurisquisque',
            'proin',
            'scelerisque',
            'lobortisac',
            'eleifend',
            'diamsuspendisse',
            'suspendisse',
            'nonummy',
            'pulvinar',
            'laciniapede',
            'dignissim',
            'ornare',
            'praesent',
            'liguladapibus',
            'nam',
            'sam',
            'lobortisquam',
            'vestibulum',
            'massa',
            'lectus',
            'nullacras',
            'pellentesque',
            'habitant',
            'senectus',
            'netuset',
            'fames',
            'egestas',
            'lobortiselit',
            'dapibusaliquam',
            'pede',
            'purus',
            'consectetuerluctus',
            'nebraska',
            'feugiatpraesent',
            'hendrerit',
            'iaculis',
            'tellusa',
            'justo',
            'eratpraesent',
            'ligulaquis',
            'tortor',
            'posuere',
            'justonullam',
            'integer',
            'rutrum',
            'facilisiquisque',
            'vel',
            'egetsemper',
            'viverra',
            'quisque',
            'dolorduis',
            'volutpat',
            'condimentum',
            'lacusnunc',
            'orcietiam',
            'mialiquam',
            'porttitor',
            'variusenim',
            'lacinia',
            'gemma',
            'ultricies',
            'fusce',
            'porttitorhendrerit',
            'ante',
            'cursus',
            'tempus',
            'felissed',
            'rhoncus',
            'idlaoreet',
            'auctor',
            'sempernisi',
            'integersem',
            'fringilla',
            'praesentet',
            'pellentesqueleo',
            'venenatis',
            'interdum',
            'semut',
            'condimentumaenean',
            'accumsan',
            'porta',
            'egetaugue',
            'faucibus',
            'consectetuerquis',
            'ultrices',
            'nontristique',
            'netus',
            'molajo',
            'turpisegestas',
            'suscipitblandit',
            'sodales',
            'blandit',
            'massaarcu',
            'famesac',
            'ligulapraesent',
            'anteipsum',
            'primis',
            'cubilia',
            'curae',
            'ipsumdonec',
            'nuncfermentum',
            'consectetuer',
            'nullainteger',
            'sapiendonec',
            'commodomauris',
            'ametultrices',
            'proinlibero',
            'adipiscingnec'
        );
    }
}
