\s+                     return TK_SPACE
[0-9]+(\.[0-9]+)+       return TK_NUMBERS
[0-9]+                  return TK_DIGITS
\.\.                    return TK_TWO_DOTS
\.                      return TK_DOT
,                       return TK_COMMA
\-                      return TK_MINUS
_                       return TK_UNDERSCORE
\$                      return TK_BAKS
:                       return TK_COLON
\+                      return TK_PLUS
<=                      return TK_LT_EQUAL
>=                      return TK_GT_EQUAL
<                       return TK_LT
>                       return TK_GT
[a-zA-Z]+               return TK_LETTERS
[a-zA-Z0-9]+            return TK_LETTERS_NUMBERS
\/                      return TK_SLASH
\*                      return TK_MULTIPLY
==                      return TK_EQUAL
=                       return TK_ASSIGMENT
!=                      return TK_NOT_EQUAL
"                       return TK_DOUBLE_QUOTE
'                       return TK_SINGLE_QUOTE
\?                      return TK_QUESTION_MARK
&&                      return TK_LOGIC_AND
\|\|                    return TK_LOGIC_OR
\|                      return TK_PIPE
\!                      return TK_EXCLAMATION_MARK
{(%|{)                  return TK_LOGIC_BEGIN
(}|%)}                  return TK_LOGIC_END
{                       return TK_FIGURE_BRAKETS_OPEN
}                       return TK_FIGURE_BRAKETS_CLOSE
\(                      return TK_BRAKETS_OPEN
\)                      return TK_BRAKETS_CLOSE
\[                      return TK_SQUARE_BRAKETS_OPEN
\]                      return TK_SQUARE_BRAKETS_CLOSE
[^\s<>0-9\.,\"\'\|a-zA-Z]+    return TK_OTHER
\n                      return TK_EOL
