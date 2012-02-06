## Fabrication Engine

### Introduction

Next generation templating engine based on the Document Object Model

The Fabrication engine or "Fabic" for short.

The Fabrication Engine represents and interacts with objects in HTML, XHTML and 
XML documents using DOM and XPath its "Elements" are addressed and manipulated 
within the syntax of public interface, direct XPath queries can be executed on 
the Document Object Model or simply use one of the many built in query methods
to create, read, update and delete also template elements structures based on
data structures.

The FabricationEngine is not like most of the other templating engine's on the 
market, maily because the engine has no concept of place holders, it is only 
concerned with elements and attributes, structures, expressions and processing 
instructions for inserting content that requires processing. 

Structures are the templates and expressions are paths to the elements contained
within the Document Object Model. The FabricationEngine extends the PHP builtin 
DOMDocument in many ways and enables the native usage of the XPath object. 
This allows for an insanely flexible and extensible document template engine.

You can create a DOM structure by loading a html, xhtml, xml string or simply by
loading a file, or you can build your own document structure by using the native
DOMDoument API. Also there is a specifcation method with the ability to recursivly
create structures.


### Features


### Contributors

* David Stevens (davro)


### License

Fabrication Engine is released under the LGPL license.


### NOTES

http://www.w3.org/TR/html401/
http://dev.w3.org/html5/spec/Overview.html#semantics

Get the title of a page:
//title/text()

Show all the alt tags:
//img/@alt

Show the href for every link:
//a/@href

Get an element with a particular CSS id:
//*[@id='mainContent']

