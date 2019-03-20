<?php


/**
 * Defines the editing form for the iiitbsql question type.
 *
 * @package    qtype
 * @subpackage iiitbsql
  */


defined('MOODLE_INTERNAL') || die();


/**
 * iiitbsql question editing form definition.
 *
  */
class qtype_iiitbsql_edit_form extends question_edit_form {

    protected function definition_inner($mform) {
        
        $mform->addElement('text','dbname','dbname');
        $menu = array(
            get_string('caseno', 'qtype_iiitbsql'),
            get_string('caseyes', 'qtype_iiitbsql')
        );
        
        $mform->addElement('select', 'usecase',
                get_string('casesensitive', 'qtype_iiitbsql'), $menu);
        
        $mform->addElement('static', 'answersinstruct',
                get_string('correctanswers', 'qtype_iiitbsql'),
                get_string('filloutoneanswer', 'qtype_iiitbsql'));
        
        
        $mform->closeHeaderBefore('answersinstruct');

        $this->add_per_answer_fields($mform, get_string('answerno', 'qtype_iiitbsql', '{no}'),
                question_bank::fraction_options(),1,1);

        $this->add_interactive_settings();
    }

    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_answers($question);
        $question = $this->data_preprocessing_hints($question);

        return $question;
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $answers = $data['answer'];
        $answercount = 0;
        $maxgrade = false;
        foreach ($answers as $key => $answer) {
            $trimmedanswer = trim($answer);
            if ($trimmedanswer !== '') {
                $answercount++;
                if ($data['fraction'][$key] == 1) {
                    $maxgrade = true;
                }
            } else if ($data['fraction'][$key] != 0 ||
                    !html_is_blank($data['feedback'][$key]['text'])) {
                $errors["answer[$key]"] = get_string('answermustbegiven', 'qtype_iiitbsql');
                $answercount++;
            }
        }
        if ($answercount==0) {
            $errors['answer[0]'] = get_string('notenoughanswers', 'qtype_iiitbsql', 1);
        }
        if ($maxgrade == false) {
            $errors['fraction[0]'] = get_string('fractionsnomax', 'question');
        }
        return $errors;
    }

    public function qtype() {
        return 'iiitbsql';
    }
}
