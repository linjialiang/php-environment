# ~/.zshrc
source ${HOME}/.profile
export ZSH="$HOME/.oh-my-zsh"
plugins=(git z)
source $ZSH/oh-my-zsh.sh
. /usr/share/zsh-autosuggestions/zsh-autosuggestions.zsh
. /usr/share/zsh-syntax-highlighting/zsh-syntax-highlighting.zsh
# PROMPT="%(?:%{$fg_bold[green]%}%n@%m ➜ :%{$fg_bold[red]%}➜ )"
PROMPT="%(?:%{$fg_bold[green]%}%n ➜ :%{$fg_bold[red]%}➜ )"
PROMPT+='%{$fg[cyan]%}%c%{$reset_color%} $(git_prompt_info)'
ZSH_THEME_GIT_PROMPT_PREFIX="%{$fg_bold[blue]%}git:(%{$fg[red]%}"
ZSH_THEME_GIT_PROMPT_SUFFIX="%{$reset_color%} "
ZSH_THEME_GIT_PROMPT_DIRTY="%{$fg[blue]%}) %{$fg[yellow]%}✗"
ZSH_THEME_GIT_PROMPT_CLEAN="%{$fg[blue]%})"
alias bat='batcat'
alias cls='clear'
setopt no_nomatch
alias l="exa -galFihH --sort=Name"
alias la="exa -aF"
PATH=${PATH}:/usr/sbin:/usr/local/sbin:sbin
PATH=${PATH}:/server/nginx/sbin
PATH=${PATH}:/server/postgres/bin
PATH=${PATH}:/server/php/84/bin:/server/php/84/sbin
PATH=${PATH}:/server/node/bin
PATH=${PATH}:/server/mysql/bin
PATH=${PATH}:/server/redis/bin
PATH=${PATH}:/server/sqlite/bin
export PATH
