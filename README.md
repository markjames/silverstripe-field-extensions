# Silverstripe Field Extensions Module

## Introduction

A SilverStripe module which adds additional template functions and methods on the default database fields (such as Dates and Strings) via extension.

## Requirements

*  SilverStripe 2.4

## Installation

* Put the module into your SilverStripe installation
* Use the fields!

## Methods

### Strings

Adds the following methods to Text, HTMLText, Varchar, HTMLVarchar and Enum:

* Widont - Adds a non-breaking space added in place of the final whitespace character to prevent the final word from sitting on a separate line due to word wrapping
* TitleCase - Runs the field through the ‘Daring Fireball’ TitleCase method
* Slugged - Gets the string as a slugged value (i.e. all non-letter characters replaced with hyphens)

### Dates

Adds the following methods to Date and SS_Datetime

* DayOfWeek - Gets the day of the week, from 1 (Monday) to 7 (Sunday)
* IsWeekend - Is the current date on a weekday?
* Iso8601 - Get the date as a correctly formatted ISO 8601 string
* Friendly - Get the date like '4 days ago', but fallback to a normal format if it goes past a certain distance from the current time
* Interval - Get the time to or from now (such as '4 hours 30 minutes') with a given level of granularity
* NiceRangeString - Get a range string between two dates that avoids showing parts twice (don't show the same year) - Not for templates!
