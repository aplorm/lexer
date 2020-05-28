Feature: Function analyse
    in order to analyse php function
    I need to find several information in each analysed function

    Scenario: basic function
        Given the piece of code :
        """
            <?php

            function test() {}
        """
        Then I found 1 "method"
        And the named "test", with "nothing"

    Scenario: public function
        Given the piece of code :
        """
            <?php

            public function test() {}
        """
        Then I found 1 "method"
        And the named "test", with "visibility=public"
        And the named "test", without "static"

    Scenario: public static function
        Given the piece of code :
        """
            <?php

            public static function test() {}
        """
        Then I found 1 "method"
        And the named "test", with "visibility=public,static"
        And the named "test", without "nullable"

    Scenario: not nullable string return function
        Given the piece of code :
        """
            <?php

            public static function test(): string {}
        """
        Then I found 1 "method"
        And the named "test", with "visibility=public,static"
        And the named "test", without "nullable"
        And the named "test", has not nullable return "string"

    Scenario: nullable string return function
        Given the piece of code :
        """
            <?php

            public static function test(): ?string {}
        """
        Then I found 1 "method"
        And the named "test", with "visibility=public,static"
        And the named "test", without "nullable"
        And the named "test", has nullable return "string"
