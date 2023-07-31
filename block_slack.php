<?php
class block_slack extends block_base {

    function init() {
        $this->title = get_string('slack', 'block_slack'); // (Title of your block).

    if ($this->page) {
        // Add CSS file only if $this->page is set
        $this->page->requires->css('/blocks/slack/block_slack.css');
    }
    }

    function has_config() {
        return true;
    }


    private function get_template_id($courseId) {
        global $DB;
    
        // Replace 'your_table_name' with the actual name of your database table
        $templateId = $DB->get_field('course_template', 'templateid', ['courseid' => $courseId]);
    
        return $templateId;
    }
    

    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }
        global $USER, $PAGE;

        $this->content = new stdClass;
        global $COURSE;
        $courseId = $COURSE->id;
        $courseName = $COURSE->fullname;
        $templateId = $this->get_template_id($courseId);
        if (!isset($PAGE)) {
            return $this->content;
        }
    
        // Add CSS styles to the page's HTML header
        $css = '
            <style>
                /* Replace these styles with your desired custom button styles */
                #certificate-button-container {
                    text-align: center;
                }
    
                .custom-button {
                    background-color: #0f6cbf;
                    color: white;
                    padding: 0px 20px;
                    border: none;
                    cursor: pointer;
                    font-size: 16px;
                    border-radius: 5px;
                    outline: none;
                    transition: background-color 0.2s;
                }
    
                .custom-button:hover {
                    background-color: #45a049;
                }
    
                .custom-button:active {
                    background-color: #3e8e41;
                }
            </style>
        ';
    
           $css_identifier = 'block_slack_custom_css';
    $PAGE->requires->data_for_js($css_identifier, $css);
        $isCompleted = $this->is_course_completed($this->page->course->id);
        // $url = new moodle_url('/blocks/slack/view_course_template.php', array('courseid' => $courseId, 'coursename' => urlencode($courseName)));

        
        // if (!$isCompleted|| !$templateId) {
        //     $this->content->text = ''; // Set content text to an empty string.
        //     $this->content->footer = ''; // Set footer to an empty string.
            
        // //     $this->content->footer = 'Your footer is displayed here';
        // } else {
            // Block is not visible if the user has not completed 100% of the course.
            $button = html_writer::tag('span', get_string('claim_certificate', 'block_slack'), array('id' => 'certificate-button', 'class' => 'custom-button'));

            $this->content->text = html_writer::div($button, 'status', array('id' => 'certificate-button-container', 'class' => 'custom-button'));
        // }

        // Include the JavaScript code
        $jscode = <<<EOT
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('certificate-button-container').addEventListener('click', function(event) {
                event.preventDefault(); // Prevent the default button behavior

                // Change the button text to "Processing..."
                this.innerText = 'Processing...';
                const courseName = '{$courseName}';

                // Get the logged-in user's name
                const userName = '{$USER->firstname} {$USER->lastname}';
                const templateId ='{$templateId}'
                console.log(templateId)
                // Perform the first API call to get the access token
                fetch('https://auth.itscredible.com/oauth2/token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'client_id=7n29ei1688c27j285sb7ivpep&client_secret=mjem3n0nr9ddm7454j4rqm0n14mf5g2u6caaesgphucolr95g83&grant_type=client_credentials'
                })
                .then(response => response.json())
                .then(data => {
                    // Handle the API response here
                    console.log(data,templateId,courseName,userName); // You can log the response to the browser's console
                    // Save the access token
                    const accessToken = data.access_token;
                    // Perform the second API call to create the document
                    fetch('https://portal.itscredible.com/api/v1/docs/create', {
                        method: 'POST',
                        headers: {
                            'Authorization': 'itscredible ' + accessToken,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            "templateId": templateId,
                            "csvData": [
                                {
                                    "Reason": courseName,
                                    "Recipient Name": userName
                                }
                            ],
                            "mappedFileNameColumn": "Recipient Name",
                            "isPrivateDoc": false
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Handle the API response here
                        debugger;
                        console.log(data?.value?.docs[0]?.docUrl); // You can log the response to the browser's console
                        window.open(data?.value?.docs[0]?.docUrl, '_blank');
                        // Update the button text after the API calls are completed
                        this.innerText = 'Claim Certificate';
                    })
                    .catch(error => {
                        // Handle errors here
                        console.error('Error:', error);
                        // Update the button text to its original state if an error occurs
                        this.innerText = 'Claim Certificate';
                    });
                })
                .catch(error => {
                    // Handle errors here
                    console.error('Error:', error);
                    // Update the button text to its original state if an error occurs
                    this.innerText = 'Claim Certificate';
                });
            });
        });
        </script>
EOT;

        $this->content->text .= $jscode;

        return $this->content;
    }

    // Create multiple instances on a page.
    public function instance_allow_multiple() {
        return true;
    }

    // Function to check if the course is completed
    private function is_course_completed($courseId) {
        global $DB, $USER;

        // Get the total number of modules in the course
        $totalModules = $DB->count_records('course_modules', array('course' => $courseId));

        // Get the total number of completed modules for the current user in the course
        $completedModules = $DB->count_records('course_modules_completion', array('coursemoduleid' => $totalModules, 'userid' => $USER->id));

        // Check if all the modules in the course are completed
        return $totalModules > 0 && $completedModules === $totalModules;
    }
}
?>
