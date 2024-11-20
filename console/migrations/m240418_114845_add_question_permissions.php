<?php

use artsoft\db\PermissionsMigration;

class m240418_114845_add_question_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('questionManagement', 'Управление разделом "Формы и заявки"');
        $this->addRole('questionAdmin', 'Администратор раздела "Формы и заявки"');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('questionManagement');
        $this->deleteRole('questionAdmin');
    }

    public function getPermissions()
    {
        return [
            'questionManagement' => [
                'links' => [
                    '/admin/question/default/*',
                    '/admin/question/default/question-attribute/*',
                    '/admin/question/default/answers/*',
                    '/question/default/*',
                    '/question/default/question-attribute/*',
                    '/question/default/answers/*',
                ],
                'viewQuestionBackend' => [
                    'title' => 'Просмотр карточек "Формы и заявки"(backend)',
                    'links' => [
                        '/admin/question/default/index',
                        '/admin/question/default/grid-sort',
                        '/admin/question/default/grid-page-size',
                        '/admin/question/default/view',
                        '/admin/question/default/download',
                        '/admin/question/default/question-attribute',
                        '/admin/question/default/question-attribute?mode=view',
                        '/admin/question/default/answers',
                        '/admin/question/default/answers?mode=view',
                    ],
                    'roles' => [
                        self::ROLE_ADMIN,
                    ],
                ],
                'editQuestionBackend' => [
                    'title' => 'Редактирование карточек "Формы и заявки"(backend)',
                    'links' => [
                        '/admin/question/default/create',
                        '/admin/question/default/update',
                        '/admin/question/default/delete',
                        '/admin/question/default/stat',
                        '/admin/question/default/question-attribute?mode=update',
                        '/admin/question/default/question-attribute?mode=history',
                        '/admin/question/default/question-attribute?mode=delete',
                        '/admin/question/default/answers?mode=create',
                        '/admin/question/default/answers?mode=update',
                        '/admin/question/default/answers?mode=history',
                        '/admin/question/default/answers?mode=delete',
                        '/admin/question/default/users-bulk-activate',
                        '/admin/question/default/users-bulk-deactivate',
                        '/admin/question/default/users-bulk-delete',
                        '/admin/question/default/bulk-send-mail',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                        'questionAdmin'
                    ],
                    'childs' => [
                        'viewQuestionBackend',
                    ],
                ],
                'viewQuestionFrontend' => [
                    'title' => 'Заполнение карточек "Формы и заявки"(frontend)',
                    'links' => [
                        '/question/default/index',
                        '/question/default/grid-sort',
                        '/question/default/grid-page-size',
                        '/question/default/view',
                        '/question/default/answers',
                        '/question/default/new',
                        '/question/default/validate',
                    ],
                    'roles' => [
                        self::ROLE_TEACHER,
                        self::ROLE_DEPARTMENT,
                        self::ROLE_EMPLOYEES,
                        self::ROLE_STUDENT,
                        self::ROLE_PARENTS,
                    ],
                ],
            ],
        ];
    }

}
