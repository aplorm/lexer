Feature: Class analyse
    in order to analyse php classes
    I need to find several information in each analysed class

    Scenario: one empty class
        Given the piece of code :
        """
            <?php

            namespace App;

            class Test {}
        """
        Then I found the "namespace" "App"
        And I found the "classname" with "className=Test"

    Scenario: one empty extended class
        Given the piece of code :
        """
            <?php

            namespace App;

            class Test Extends ParentClass {}
        """
        Then I found the "namespace" "App"
        And I found the "classname" with "className=Test,parent=ParentClass"

    Scenario: one empty class with declare
        Given the piece of code :
        """
            <?php
            declare(strict_types=1);

            namespace App;

            class Test {}
        """
        Then I found the "namespace" "App"
        And I found the "classname" with "className=Test"

    Scenario: class with uses
        Given the piece of code :
        """
            <?php

            namespace App;

            use App\Foo;
            use Bar\Foo AS FooBar;

            class Test {}
        """
        Then I found 2 "classalias", with "Foo=App\Foo,FooBar=Bar\Foo"

    Scenario: class with grouped uses
        Given the piece of code :
        """
            <?php

            namespace App;

            use App\Foo;
            use Bar\{ Foo AS FooBar, Bar};

            class Test {}
        """
        Then I found 3 "classalias", with "Bar=Bar\Bar,FooBar=Bar\Foo"

    Scenario: class with variables
        Given the piece of code :
        """
            <?php

            namespace App;

            class Test {
                private $attr;
            }
        """
        Then in class I found 1 "variables"
        And the "attr" "variables", with "visibility=private"
        And the "attr" "variables", without "nullable"

    Scenario: class with annotations variables
        Given the piece of code :
        """
            <?php

            namespace App;

            class Test {
                /**
                 * @annotation
                 */
                private $attr;
            }
        """
        Then in class I found 1 "variables"
        And the "attr" "variables", with "visibility=private"
        And the "attr" "variables", with 1 annotation named "annotation"

    Scenario: class with multiple variables
        Given the piece of code :
        """
            <?php

            namespace App;

            class Test {
                private $attr, $attr2;
            }
        """
        Then in class I found 2 "variables"

    Scenario: class with local trait
        Given the piece of code :
        """
            <?php

            namespace App;

            class Test {
                use MyTrait;
            }
        """
        Then I found 1 "traits", with "MyTrait=App\MyTrait"

    Scenario: class with trait
        Given the piece of code :
        """
            <?php

            namespace App;

            use Foo\MyTrait;

            class Test {
                use MyTrait;
            }
        """
        Then I found 1 "traits", with "MyTrait=Foo\MyTrait"

    Scenario: class with function
        Given the piece of code :
        """
            <?php

            namespace App;

            use Foo\MyTrait;

            class Test {
                public function aFunction() {}
            }
        """
        Then in class I found 1 "method"
        And the "aFunction" "method", with "visibility=public"

    Scenario: trait with function
        Given the piece of code :
        """
            <?php

            namespace App;

            use Foo\MyTrait;

            trait Test {
                public function aFunction() {}
            }
        """
        Then in class I found 1 "method"
        And I found the "classname" with "className=Test,isTrait"
        And the "aFunction" "method", with "visibility=public"

    Scenario: class with annoted function
        Given the piece of code :
        """
            <?php

            namespace App;

            use Foo\MyTrait;

            class Test {
                /**
                 * @annotation
                 */
                public function aFunction() {}
            }
        """
        Then in class I found 1 "method"
        And the "aFunction" "method", with "visibility=public"
        And the "aFunction" "method", with 1 annotation named "annotation"

