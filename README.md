+------------------------------------------------------------------------------+
|
|  Fabrication Engine
|
+------------------------------------------------------------------------------+

### Introduction

Next generation templating engine based on the Document Object Model

The Fabrication Engine represents and interacts with objects in HTML, XHTML and 
XML documents using DOM and XPath its "Elements" are addressed and manipulated 
within the syntax of public interface, direct XPath queries can be executed on 
Fab Document Object Model or simply use one of the many built in query methods.

The FabricationEngine is not like most of the other templating engine's on the 
market, because the "Fab" engine has no concept of place holders Fab is only 
concerned with structures, expressions and processing instructions. 

Structures are the templates and expressions are paths to the elements contained
within the Document Object Model. The FabricationEngine extends the PHP builtin 
DOMDocument in every way and enables the XPath object. This allows for an 
insanely flexible and extensible document template engine, without placeholders.

The Fabrication engine or "Fabic" for short uses XPath to manipulate the DOM you 
can create a DOM structure by loading a html, xhtml, xml string or simply by
loading a file, or you can build your own document structure by using the native
DOMDoument API.


### Features


### Contributors

* David Stevens (davro)


### License

Fabrication Engine is released under the LGPL license.


### NOTES

http://www.w3.org/TR/html401/


Get the title of a page:
//title/text()

Show all the alt tags:
//img/@alt

Show the href for every link:
//a/@href

Get an element with a particular CSS id:
//*[@id='mainContent']

http://dev.w3.org/html5/spec/Overview.html#semantics

# The root element
html element

    # Document metadata
    head element
        title element
        base element
        link element
        meta element
        style element

        ## Scripting
        script element
        noscript element

    # Sections
    body element
        section element
        nav element
        article element
        aside element
        h1, h2, h3, h4, h5, and h6 elements
        hgroup element
        header element
        footer element
        address element

        ## Grouping content
        p element
        hr element
        pre element
        blockquote element
        ol element
        ul element
        li element
        dl element
        dt element
        dd element
        figure element
        figcaption element
        div element

        ## Text-level semantics
        a element
        em element
        strong element
        small element
        s element
        cite element
        q element
        dfn element
        abbr element
        time element
        code element
        var element
        samp element
        kbd element
        sub and sup elements
        i element
        b element
        u element
        mark element
        ruby element
        rt element
        rp element
        bdi element
        bdo element
        span element
        br element
        wbr element
        summary

        ## Edits
        ins element
        del element

        ## Embedded content
        img element
        iframe element
        embed element
        object element
        param element
        video element
        audio element
        source element
        track element
        canvas element
        map element
        area element

        ## Forms
        form element
        fieldset element
        legend element
        label element
        input element
        button element
        select element
        datalist element
        optgroup element
        option element
        textarea element
        keygen element
        output element
        progress element
        meter element

        ## Interactive elements
        details element
        summary element
        command element
        menu element

        ## Bindings
        button element
        details element
        input element as a text entry widget
        input element as domain-specific widgets
        input element as a range control
        input element as a color well
        input element as a checkbox and radio button widgets
        input element as a file upload control
        input element as a button
        marquee element
        meter element
        progress element
        select element
        textarea element
        keygen element
        time element

        ## Obsolete features
        applet element
        marquee element
