Feature: Annotation analyse
    in order to analyse doc block to find annotation
    I need to find several information in each docblock analyse

    Scenario: one simple annotation
        Given the piece of comment :
        """
        /**
         * @annotation
         */
        """
        Then I found 1 annotation
        And the 1 is named "annotation"

    Scenario: one simple annotation with parameter
        Given the piece of comment :
        """
        /**
         * @annotation("string")
         */
        """
        Then I found 1 annotation
        And the 1 is named "annotation"
        And with 1 parameter in the 1 annotation
        And the 1 parameter with "string" has value in the 1 annotation

    Scenario: one simple annotation with an array has parameter
        Given the piece of comment :
        """
        /**
         * @annotation({
            'key1': true,
            'key2': [1, 2]
         })
         */
        """
        Then I found 1 annotation
        And the 1 is named "annotation"
        And with 1 parameter in the 1 annotation
        And the 1 parameter of the 1 annotation with array value:
        """
        {
            "key1": {
                "type": 3,
                "value": "true"
            },
            "key2": {
                "type": 5,
                "value": [
                    {
                        "type": 8,
                        "value": "1"
                    },
                    {
                        "type": 8,
                        "value": "2"
                    }
                ]
            }
        }
        """

    Scenario: one simple annotation with an array has parameter with equals
        Given the piece of comment :
        """
        /**
         * @annotation({
            'key1'= true,
            'key2'= [1, 2]
         })
         */
        """
        Then I found 1 annotation
        And the 1 is named "annotation"
        And with 1 parameter in the 1 annotation
        And the 1 parameter of the 1 annotation with array value:
        """
        {
            "key1": {
                "type": 3,
                "value": "true"
            },
            "key2": {
                "type": 5,
                "value": [
                    {
                        "type": 8,
                        "value": "1"
                    },
                    {
                        "type": 8,
                        "value": "2"
                    }
                ]
            }
        }
        """

    Scenario: one simple annotation with an array has parameter with embeded annotation
        Given the piece of comment :
        """
        /**
         * @annotation({
            'key1'= @annotation2,
            'key2'= [1, 2]
         })
         */
        """
        Then I found 1 annotation
        And the 1 is named "annotation"
        And with 1 parameter in the 1 annotation
        And the 1 parameter of the 1 annotation with array value:
        """
        {
            "key1": {
                "type": 6,
                "value": {
                    "name": "annotation2",
                    "params": []
                }
            },
            "key2": {
                "type": 5,
                "value": [
                    {
                        "type": 8,
                        "value": "1"
                    },
                    {
                        "type": 8,
                        "value": "2"
                    }
                ]
            }
        }
        """

    Scenario: one simple annotation with named parameter
        Given the piece of comment :
        """
        /**
         * @annotation(param = true)
         */
        """
        Then I found 1 annotation
        And the 1 is named "annotation"
        And with 1 parameter in the 1 annotation
        And the 1 parameter with "true" has value and "param" has name in the 1 annotation

    Scenario: one simple annotation with escaped string
        Given the piece of comment :
        """
        /**
         * @annotation('it\'s a string')
         */
        """
        Then I found 1 annotation
        And the 1 is named "annotation"
        And with 1 parameter in the 1 annotation
        And the 1 parameter with "it's a string" has value in the 1 annotation

    Scenario: one simple annotation with class constant
        Given the piece of comment :
        """
        /**
         * @annotation(Class::A_CONSTANT)
         */
        """
        Then I found 1 annotation
        And the 1 is named "annotation"
        And with 1 parameter in the 1 annotation
        And the 1 parameter with "Class::A_CONSTANT" has value in the 1 annotation

    Scenario: one simple annotation with float value
        Given the piece of comment :
        """
        /**
         * @annotation(1.5)
         */
        """
        Then I found 1 annotation
        And the 1 is named "annotation"
        And with 1 parameter in the 1 annotation
        And the 1 parameter with "1.5" has value in the 1 annotation

    Scenario: one simple annotation with new long int value
        Given the piece of comment :
        """
        /**
         * @annotation(1_500)
         */
        """
        Then I found 1 annotation
        And the 1 is named "annotation"
        And with 1 parameter in the 1 annotation
        And the 1 parameter with "1_500" has value in the 1 annotation

    Scenario: one simple annotation with other constant int value
        Given the piece of comment :
        """
        /**
         * @annotation(PHP_EOL)
         */
        """
        Then I found 1 annotation
        And the 1 is named "annotation"
        And with 1 parameter in the 1 annotation
        And the 1 parameter with "PHP_EOL" has value in the 1 annotation


