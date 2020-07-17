Feature: annotation > simple annotations
  this scenario will test the parsing of a very simple annotations such as `@Annotation`
  each test will go deeper into the process

  The first step of finding an annotation into a docblock is to parse each characters to find and interprete annotation.


  Scenario: getting an empty object jar
    Given nothing
    Then the TokenJar object should contains 0 characters

  Scenario: fill an object jar
    Given a docblock:
    """
    /**
    */
    """
    Then the TokenJar object should contains 5 characters
