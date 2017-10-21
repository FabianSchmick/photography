# Start
# #############

# if we're not an interactive shell, do nothing
if [ -z "$PS1" ]; then
    return
fi

# Shell Options
# #############

# Use case-insensitive filename globbing
shopt -s nocaseglob

# Make bash append rather than overwrite the history on disk
shopt -s histappend

# When changing directory small typos can be ignored by bash
# for example, cd /vr/lgo/apaache would find /var/log/apache
shopt -s cdspell

# Completion options
# ##################

# These completion tuning parameters change the default behavior of bash_completion:

# Define to access remotely checked-out files over passwordless ssh for CVS
# COMP_CVS_REMOTE=1

# Define to avoid stripping description in --option=description of './configure --help'
# COMP_CONFIGURE_HINTS=1

# Define to avoid flattening internal contents of tar files
# COMP_TAR_INTERNAL_PATHS=1

# If this shell is interactive, turn on programmable completion enhancements.
# Any completions you add in ~/.bash_completion are sourced last.
# case $- in
#   *i*) [[ -f /etc/bash_completion ]] && . /etc/bash_completion ;;
# esac

# History Options
# ###############

# Don't put duplicate lines in the history.
export HISTCONTROL="ignoredups"
export HISTCONTROL="erasedups"

# Big History
export HISTSIZE=100000

# Aliases
# #######

# Some example alias instructions
# If these are enabled they will be used instead of any instructions
# they may mask.  For example, alias rm='rm -i' will mask the rm
# application.  To override the alias instruction use a \ before, ie
# \rm will call the real rm not the alias.

# Colors
# alias ls='ls --color=always -h'

# Functions
# #########

# Some example functions
# function settitle() { echo -ne "\e]2;$@\a\e]1;$@\a"; }

# Other Stuff
# #########

# setup default language
export LANG="en_EN.UTF-8"

# setup pager
export PAGER="less"

# mysql prompt tuning
export MYSQL_PS1='(\u@\h) [\d]> '

# Prompt Colors
# #############

echo -en "\e]P0000000" #black
echo -en "\e]P8505354" #darkgrey
echo -en "\e]P1f92672" #darkred
echo -en "\e]P9ff5995" #red
echo -en "\e]P282b414" #darkgreen
echo -en "\e]PAb6e354" #green
echo -en "\e]P3fd971f" #brown
echo -en "\e]PBfeed6c" #yellow
echo -en "\e]P456c2d6" #darkblue
echo -en "\e]PC8cedff" #blue
echo -en "\e]P58c54fe" #darkmagenta
echo -en "\e]PD9e6ffe" #magenta
echo -en "\e]P6465457" #darkcyan
echo -en "\e]PE899ca1" #cyan
echo -en "\e]P7ccccc6" #lightgrey
echo -en "\e]PFf8f8f2" #white

Black='\e[0;30m'    # Black / Regular
Red='\e[0;31m'      # Red
Green='\e[0;32m'    # Green
Yellow='\e[0;33m'   # Yellow
Blue='\e[0;34m'     # Blue
Purple='\e[0;35m'   # Purple
Cyan='\e[0;36m'     # Cyan
White='\e[0;37m'    # White

BBlack='\e[1;30m'   # BBlack / Bold
BRed='\e[1;31m'     # BRed
BGreen='\e[1;32m'   # BGreen
BYellow='\e[1;33m'  # BYellow
BBlue='\e[1;34m'    # BBlue
BPurple='\e[1;35m'  # BPurple
BCyan='\e[1;36m'    # BCyan
BWhite='\e[1;37m'   # BWhite

UBlack='\e[4;30m'   # UBlack / Underline
URed='\e[4;31m'     # URed
UGreen='\e[4;32m'   # UGreen
UYellow='\e[4;33m'  # UYellow
UBlue='\e[4;34m'    # UBlue
UPurple='\e[4;35m'  # UPurple
UCyan='\e[4;36m'    # UCyan
UWhite='\e[4;37m'   # UWhite

BGBlack='\e[40m'    # BGBlack - background
BGRed='\e[41m'      # BGRed
BGGeeen='\e[42m'    # BGGreen
BGYellow='\e[43m'   # BGYellow
BGBlue='\e[44m'     # BGBlue
BGPurple='\e[45m'   # BGPurple
BGCyan='\e[46m'     # BGCyan
BGWhite='\e[47m'    # BGWhite

NC='\e[0m'          # Text Reset / No Color

case $TERM in
  screen*)
    SCREENTITLE='\[\ek\e\\\]\[\ek\W\e\\\]'
  ;;
  *)
    SCREENTITLE=''
  ;;
esac

USER=`whoami`

function prompt_func() {
prompt="\n${Cyan}\t ${USER,,}@\h ${TITLEBAR}${Blue}[${BRed}\w${Green}${Blue}]${NC}"
# When you have colors and other non-printing escape sequences in your prompt, you have to surround them with escaped single brackets. \[${NC}\]
PS1="${SCREENTITLE}${prompt}${White}\n ➔ \[${NC}\]"  # ➔ = \342\236\224
}

PROMPT_COMMAND=prompt_func
