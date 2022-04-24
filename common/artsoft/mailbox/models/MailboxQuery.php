<?php

namespace artsoft\mailbox\models;

/**
 * This is the ActiveQuery class for [[Mailbox]].
 *
 * @see Mailbox
 */
class MailboxQuery extends \yii\db\ActiveQuery
{
    public function readNew()
    {
        return $this->where(['=', 'status_read', Mailbox::STATUS_READ_NEW]);
    }

    /**
     * {@inheritdoc}
     * @return Mailbox[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Mailbox|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
