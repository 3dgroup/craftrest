<?php
namespace threedgroup\craftrest\models;

use craft\base\Model;

class Settings extends Model
{
    public $customElements;
    public $sections;

    public $entryPath = 'api';
}
