<?php

namespace common\models\studyplan;

/**
 * This is the ActiveQuery class for [[Studyplan]].
 *
 * @see Studyplan
 */
class StudyplanQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere(['OR',
            ['status' => Studyplan::STATUS_ACTIVE],
            ['AND',
                ['status' => Studyplan::STATUS_INACTIVE],
                ['status_reason' => [1, 2, 3, 4]]
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     * @return Studyplan[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Studyplan|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
