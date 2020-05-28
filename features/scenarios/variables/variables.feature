Feature: Variable analyse
    in order to analyse php variables
    I need to find several information in each analysed variables

    Scenario: one variable without description
        Given the piece of code :
        """
            <?php

            $variable;
        """
        Then I found 1 "variables"
        And the named "variable", with "nothing"

    Scenario: one private variable
        Given the piece of code :
        """
            <?php

            private $variable;
        """
        Then I found 1 "variables"
        And the named "variable", with "visibility=private"

    Scenario: one private variable with type
        Given the piece of code :
        """
            <?php

            private int $variable;
        """
        Then I found 1 "variables"
        And the named "variable", with "visibility=private,type=int"
        And the "variable" hasn't "nullable"

    Scenario: one private variable with nullable type
        Given the piece of code :
        """
            <?php

            private ?int $variable;
        """
        Then I found 1 "variables"
        And the named "variable", with "visibility=private,type=int"
        And the "variable" has "nullable"

    Scenario: one variable with default value
        Given the piece of code :
        """
            <?php

            private ?int $variable = 1;
        """
        Then I found 1 "variables"
        And the named "variable", with "visibility=private,type=int"
        And the "variable" has "nullable"
        And the "variable" with "1" has default value

    Scenario: one variable with constant has default value
        Given the piece of code :
        """
            <?php

            private ?int $variable = AClass::DEFAULT_CONSTANT;
        """
        Then I found 1 "variables"
        And the named "variable", with "visibility=private,type=int"
        And the "variable" has "nullable"
        And the "variable" with "AClass::DEFAULT_CONSTANT" has default value

    Scenario: one variable with array has default value
        Given the piece of code :
        """
            <?php

            private ?array $variable = [['a' => 'b'], 'a' => 'b', 'a'];
        """
        Then I found 1 "variables"
        And the named "variable", with "visibility=private,type=array"
        And the "variable" has "nullable"
        And the "variable" with array value
        """
            {"0":{"a":"b"},"a":"b","1":"a"}
        """

    Scenario: one constant
        Given the piece of code :
        """
            <?php

            private const VARIABLE = [['a' => 'b'], 'a' => 'b', 'a'];
        """
        Then I found 0 "variables"

