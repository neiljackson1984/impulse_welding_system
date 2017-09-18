# design_framework
This is a set of tools (at the moment, just a single php script called 'equationProcessor.php' to take a php script, composed in a certain way, and spit out a file to be digested by my Solidworks propertyImporter.
I intend that the root directory of this project be added (either by plain-old copying, as a git submodule, or as a subdirectory to be kept in sync with this repository by means of 'git merge -s subtree') as a subdirectory to each project where this framework is to be employed.

This project shall be agnostic to the particular way that it happens to be added as a subproject to other projects.

The makefile 'makefile.sample' is a useful starting-point for the makefile to be used in the project in which this project is added as a subproject.
