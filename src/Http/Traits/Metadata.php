<?php

namespace Humweb\Core\Http\Traits;

/**
 * Metadata
 *
 * @package ${NAMESPACE}
 */
trait Metadata
{
    protected $_metadata = [];


    public function setMeta($name, $content, $type = 'meta')
    {
        switch ($type) {
            case 'link':
                $this->_metadata[] = '<link rel="'.$name.'" href="'.$content.'" />';
                break;

            case 'og':
                $this->_metadata[] = '<meta property="'.$name.'" content="'.$content.'" />';
                break;
            case 'meta':
            default:
                $this->_metadata[] = '<meta name="'.$name.'" content="'.$content.'" />';
        }
    }


    public function shareMetadataWithView($view)
    {
        if ($this->hasMetadata()) {
            $view->with('metadata', implode(PHP_EOL, $this->_metadata));
        }
    }


    public function hasMetadata()
    {
        return ! empty($this->_metadata);
    }
}