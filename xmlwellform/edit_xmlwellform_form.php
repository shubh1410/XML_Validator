<?php


/**
 * Defines the editing form for the xmlwellform question type.
 *
 * @package    qtype
 * @subpackage xmlwellform
  */


defined('MOODLE_INTERNAL') || die();


/**
 * xmlwellform question editing form definition.
 *
  */
class qtype_xmlwellform_edit_form extends question_edit_form {

    protected function definition_inner($mform) {


        $menu = array(
            get_string('caseno', 'qtype_xmlwellform'),
            get_string('caseyes', 'qtype_xmlwellform')
        );

        $mform->addElement('select', 'usecase',
                get_string('casesensitive', 'qtype_xmlwellform'), $menu);

        //$mform->addElement('static', 'answersinstruct',
              //  get_string('correctanswers', 'qtype_xmlwellform'),
              //  get_string('filloutoneanswer', 'qtype_xmlwellform'));


        $mform->closeHeaderBefore('answersinstruct');

        $this->add_per_answer_fields($mform,"Answer ",//get_string('answerno', 'qtype_xmlwellform', '{no}'),
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
            if ($trimmedanswer == '')
            {
              $trimmedanswer = trim("~");
            }
            if ($trimmedanswer !== '') {
                $answercount++;
                if ($data['fraction'][$key] == 1) {
                    $maxgrade = true;
                }
            } else if ($data['fraction'][$key] != 0 ||
                    !html_is_blank($data['feedback'][$key]['text'])) {
                $errors["answer[$key]"] = get_string('answermustbegiven', 'qtype_xmlwellform');
                $answercount++;
            }
        }
        if ($answercount==0) {
            $errors['answer[0]'] = get_string('notenoughanswers', 'qtype_xmlwellform', 1);
        }
        if ($maxgrade == false) {
            $errors['fraction[0]'] = get_string('fractionsnomax', 'question');
        }
        return $errors;
    }

    public function qtype() {
        return 'xmlwellform';
    }
}
