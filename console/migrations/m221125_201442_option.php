<?php

use \artsoft\db\BaseMigration;

class m221125_201442_option extends BaseMigration
{
    public function up()
    {
        $this->db->createCommand()->createView('schoolplan_view', '
        SELECT id, 
                author_id, 
                title, 
                datetime_in, 
                datetime_out, 
                places,
                auditory_id,
                CASE
                      WHEN (places IS NOT NULL) THEN places
                      WHEN (auditory_id  IS NOT NULL) THEN (SELECT concat(auditory.num, \' - \', auditory.name) FROM auditory WHERE id = auditory_id)
                      ELSE NULL
                    END AS auditory_places,
                department_list, 
                executors_list, 
                category_id, 
                activities_over_id, 
                form_partic, 
                partic_price, 
                visit_poss, 
                visit_content, 
                format_event, 
                important_event, 
                region_partners, 
                site_url, 
                site_media, 
                description, 
                rider, 
                result, 
                num_users, 
                num_winners, 
                num_visitors, 
                bars_flag, 
                created_at, 
                created_by, 
                updated_at, 
                updated_by, 
                version, 
                doc_status
	FROM schoolplan;
        ')->execute();
    }

        public function down()
    {
        $this->db->createCommand()->dropView('schoolplan_view')->execute();
    }
}
