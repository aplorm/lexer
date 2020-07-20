Feature: annotation > simple annotations
  this scenario will test the parsing of a very simple annotations such as `@Annotation`
  each test will go deeper into the process

  The first step of finding an annotation into a docblock is to parse each characters to find and interprete annotation.


  Scenario: getting an empty object jar
    Given nothing
    Then the StringTokenJar object should contains 0 characters

  Scenario: fill an object jar
    Given a docblock:
    """
    /**
    */
    """
    Then the StringTokenJar object should contains 5 characters


  Scenario: getting the first token of an empty object jar
    Given nothing
    Then the StringTokenJar object should contains null for the first token

  Scenario: getting the first token of an object jar
    Given a docblock:
    """
    /**
    */
    """
    Then the StringTokenJar object should contains '/' for the first token

  Scenario: going to the next token in empty docBlock
    Given nothing
    When calling next function, the response must be null

  Scenario: going to the next token in an object Jar
    Given a docblock:
    """
    /**
    */
    """
    When calling next function, the response must be '*'

  Scenario: Read completely a docBlock
    Given nothing
    Then the DocBlock find array
    """
    {}
    """

  Scenario: find an annotation in docBlock
    Given a docblock:
    """
    /**
    * @annotation
    */
    """
    Then the DocBlock find array
    """
    {"0":"annotation"}
    """

  Scenario: get parameters in annotation
    Given a docblock:
    """
    /**
    * @annotation
    */
    """
    Then the "annotation" has the next array no parameter

  Scenario: get parameters in annotation
    Given a docblock:
    """
    /**
    * @annotation(1)
    */
    """
    Then the "annotation" has the next array has parameter
    """
    {"0":"1"}
    """

