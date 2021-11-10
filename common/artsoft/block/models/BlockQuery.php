<?php

namespace artsoft\block\models;

/**
 * This is the ActiveQuery class for [[Block]].
 *
 * @see Block
 */
class BlockQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return Block[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Block|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

}
