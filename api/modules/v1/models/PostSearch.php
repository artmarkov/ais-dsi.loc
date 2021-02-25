<?php

namespace api\modules\v1\models;

class PostSearch extends \artsoft\post\models\search\PostSearch
{    
    public function formName()
    {
        return 's';
    }
}
