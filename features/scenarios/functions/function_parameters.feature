Feature: Function analyse
    in order to analyse php function with parameters
    I need to find several information in each analysed function

    Scenario: one parameter without description
        Given the piece of code :
        """
            <?php

            function test($str) {}
        """
        Then I found 1 "method"
        And the named "test" has 1 parameters
        And the "str" parameters has "name=str,nullable"

    Scenario: two parameter without description
        Given the piece of code :
        """
            <?php

            function test($str, $int) {}
        """
        Then I found 1 "method"
        And the named "test" has 2 parameters
        And the "int" parameters has "name=int,nullable"

    Scenario: two parameter on multi line without description
        Given the piece of code :
        """
            <?php

            function test(
                $str = "1",
                $int = 1)
            {}
        """
        Then I found 1 "method"
        And the named "test" has 2 parameters
        And the "int" parameters has "name=int,nullable"

    Scenario: one parameter typed
        Given the piece of code :
        """
            <?php

            function test(string $str) {}
        """
        Then I found 1 "method"
        And the named "test" has 1 parameters
        And the "str" parameters has "name=str,type=string"
        And the "str" parameters hasn't "nullable"

    Scenario: 1 parameter with default value
        Given the piece of code :
        """
            <?php

            function test(string $str = "bla") {}
        """
        Then I found 1 "method"
        And the named "test" has 1 parameters
        And the "str" parameters has "name=str,type=string"
        And the "str" parameters hasn't "nullable"
        And the "str" parameters with "bla" has default value

    Scenario: 1 parameter with reference
        Given the piece of code :
        """
            <?php

            function test(string &$str = "bla") {}
        """
        Then I found 1 "method"
        And the named "test" has 1 parameters
        And the "str" parameters has "name=str,type=string"
        And the "str" parameters hasn't "nullable"
        And the "str" parameters with "bla" has default value

    Scenario: 2 parameter with heredoc
        Given the piece of code :
        """
            <?php

            function test(string $param1 = <<<'EOT'
        bla bla
    EOT , array $param2 = [['A' => 'B'], ['A', 'B'], 'A']) {}
        """
        Then I found 1 "method"
        And the named "test" has 2 parameters
        And the "param1" parameters has "name=param1,type=string"
        And the "param1" parameters hasn't "nullable"
        And the "param1" parameters with "bla bla" has default value
        And the "param2" parameters has "name=param2,type=array"
        And the "param2" parameters hasn't "nullable"
        And the "param2" parameters with array value
        """
            {"0":{"A":"B"},"1":{"0":"A","1":"B"},"2":"A"}
        """

    Scenario: 1 parameter with array default value
        Given the piece of code :
        """
            <?php

            function test(array $array = [['a' => 'b'], 'a' => 'b', 'a']) {}
        """
        Then I found 1 "method"
        And the named "test" has 1 parameters
        And the "array" parameters has "name=array,type=array"
        And the "array" parameters hasn't "nullable"
        And the "array" parameters with array value
        """
            {"0":{"a":"b"},"a":"b","1":"a"}
        """

    Scenario: 1 parameter with old array default value
        Given the piece of code :
        """
            <?php

            function test(array $array = array(['a' => 'b'], 'a' => 'b', 'a')) {}
        """
        Then I found 1 "method"
        And the named "test" has 1 parameters
        And the "array" parameters has "name=array,type=array"
        And the "array" parameters hasn't "nullable"
        And the "array" parameters with array value
        """
            {"0":{"a":"b"},"a":"b","1":"a"}
        """

    Scenario: function with typed return
        Given the piece of code :
        """
            <?php

            function test(): int
            {}
        """
        Then I found 1 "method"
        And the named "test" will return "int" with nullable is "false"

    Scenario: function with typed nullabe return
        Given the piece of code :
        """
            <?php

            function test(): ?int
            {}
        """
        Then I found 1 "method"
        And the named "test" will return "int" with nullable is "true"

    Scenario: abstract function with typed nullabe return
        Given the piece of code :
        """
            <?php

            function test(): ?int;
        """
        Then I found 1 "method"
        And the named "test" will return "int" with nullable is "true"

    Scenario: function with complexe content
        Given the piece of code :
        """
            <?php

            function test(): ?int
            {
                $a = function() {
                    $bool = true;
                    if ($bool) {
                        return;
                    }
                };
            }
        """
        Then I found 1 "method"
        And the named "test" will return "int" with nullable is "true"
