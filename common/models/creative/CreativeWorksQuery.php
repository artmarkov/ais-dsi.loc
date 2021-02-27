<?php

namespace common\models\creative;

/**
 * This is the ActiveQuery class for [[CreativeWorks]].
 *
 * @see CreativeWorks
 */
class CreativeWorksQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return CreativeWorks[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return CreativeWorks|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
