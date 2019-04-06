<?php


/**
 * Defines the editing form for the xmlvalidator question type.
 *
 * @package    qtype
 * @subpackage xmlvalidator
  */


defined('MOODLE_INTERNAL') || die();


/**
 * xmlvalidator question editing form definition.
 *
  */
class qtype_xmlvalidator_edit_form extends question_edit_form {

    protected function definition_inner($mform) {


        $menu = array(
            get_string('caseno', 'qtype_xmlvalidator'),
            get_string('caseyes', 'qtype_xmlvalidator')
        );

        $mform->addElement('select', 'usecase',
                get_string('casesensitive', 'qtype_xmlvalidator'), $menu);

        //$mform->addElement('static', 'answersinstruct',
              //  get_string('correctanswers', 'qtype_xmlvalidator'),
              //  get_string('filloutoneanswer', 'qtype_xmlvalidator'));


        $mform->closeHeaderBefore('answersinstruct');

        $this->add_per_answer_fields($mform,'XSD_File : ' ,//get_string('answerno', 'qtype_xmlvalidator', '{no}'),
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
                $errors["answer[$key]"] = get_string('answermustbegiven', 'qtype_xmlvalidator');
                $answercount++;
            }
        }
        if ($answercount==0) {
            $errors['answer[0]'] = get_string('notenoughanswers', 'qtype_xmlvalidator', 1);
        }
        if ($maxgrade == false) {
            $errors['fraction[0]'] = get_string('fractionsnomax', 'question');
        }
        return $errors;
    }

    public function qtype() {
        return 'xmlvalidator';
    }
}
