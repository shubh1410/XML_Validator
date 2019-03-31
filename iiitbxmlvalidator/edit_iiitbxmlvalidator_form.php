<?php


/**
 * Defines the editing form for the iiitbxmlvalidator question type.
 *
 * @package    qtype
 * @subpackage iiitbxmlvalidator
  */


defined('MOODLE_INTERNAL') || die();


/**
 * iiitbxmlvalidator question editing form definition.
 *
  */
class qtype_iiitbxmlvalidator_edit_form extends question_edit_form {

    protected function definition_inner($mform) {


        $menu = array(
            get_string('caseno', 'qtype_iiitbxmlvalidator'),
            get_string('caseyes', 'qtype_iiitbxmlvalidator')
        );

        $mform->addElement('select', 'usecase',
                get_string('casesensitive', 'qtype_iiitbxmlvalidator'), $menu);

        $mform->addElement('static', 'answersinstruct',
                get_string('correctanswers', 'qtype_iiitbxmlvalidator'),
                get_string('filloutoneanswer', 'qtype_iiitbxmlvalidator'));


        $mform->closeHeaderBefore('answersinstruct');

        $this->add_per_answer_fields($mform, get_string('answerno', 'qtype_iiitbxmlvalidator', '{no}'),
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
                $errors["answer[$key]"] = get_string('answermustbegiven', 'qtype_iiitbxmlvalidator');
                $answercount++;
            }
        }
        if ($answercount==0) {
            $errors['answer[0]'] = get_string('notenoughanswers', 'qtype_iiitbxmlvalidator', 1);
        }
        if ($maxgrade == false) {
            $errors['fraction[0]'] = get_string('fractionsnomax', 'question');
        }
        return $errors;
    }

    public function qtype() {
        return 'iiitbxmlvalidator';
    }
}
