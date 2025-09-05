<?php

use yii\db\Migration;

/**
 * Handles adding indexes to responses and answers tables to speed up analytics queries.
 */
class m250905_000000_add_response_answer_indexes extends Migration
{
    public function safeUp()
    {
        // responses table indexes
        $this->createIndex('idx-responses-survey_id', '{{%responses}}', 'survey_id');
        $this->createIndex('idx-responses-agent_id', '{{%responses}}', 'agent_id');
        $this->createIndex('idx-responses-created_at', '{{%responses}}', 'created_at');

        // answers table indexes
        $this->createIndex('idx-answers-response_id', '{{%answers}}', 'response_id');
        $this->createIndex('idx-answers-question_id', '{{%answers}}', 'question_id');
    }

    public function safeDown()
    {
        $this->dropIndex('idx-responses-survey_id', '{{%responses}}');
        $this->dropIndex('idx-responses-agent_id', '{{%responses}}');
        $this->dropIndex('idx-responses-created_at', '{{%responses}}');

        $this->dropIndex('idx-answers-response_id', '{{%answers}}');
        $this->dropIndex('idx-answers-question_id', '{{%answers}}');
    }
}
