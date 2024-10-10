## allow Ctrl+s pressing
/bin/stty start ^- ; /bin/stty stop ^-

## set creation access mode -rw-r--r--  and drwxr-xr-x 
umask 022

## If not running interactively, don't do anything
case $- in
    *i*) ;;
      *) return;;
esac

## don't put duplicate lines or lines starting with space in the history.
HISTCONTROL=ignoreboth

## append to the history file, don't overwrite it
shopt -s histappend

## for setting history length see HISTSIZE and HISTFILESIZE in bash(1)
HISTSIZE=2048
HISTFILESIZE=8192

## check the window size after each command and, if necessary, update the values of LINES and COLUMNS.
shopt -s checkwinsize

## some more ls aliases
alias ssh='ssh -o ServerAliveInterval=5'
alias sftp='sftp -o ServerAliveInterval=5'
alias mc='mc -d'
alias dir='dir -a -b -1 --group-directories-first -p -l -h --time-style=iso'

## like kali prompt but more modest
export PS1="\n┌┤ \u \w \n└─ "

