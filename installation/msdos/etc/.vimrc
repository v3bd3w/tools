syntax off
set autoindent

highlight MatchParen ctermfg=DarkRed ctermbg=Black
highlight Folded ctermfg=DarkGrey ctermbg=Black

" zc zo close open fold zM zR close open All folds
set foldmethod=marker

set tabstop=4
set softtabstop=4
set shiftwidth=4
"set expandtab
set nowrap
set noswapfile
set showtabline=2

" open
imap <C-O> <C-[>:e 
nmap <C-O> :e 
" new tab
imap <C-T> <C-[>:tabnew 
nmap <C-T> :tabnew 
" quit
imap <C-W> <C-[>:q<CR>
nmap <C-W> :q<CR>
" forced quit
imap <C-Q> <C-[>:q!<CR>
nmap <C-Q> :q!<CR>
" save
imap <C-S> <C-[>:w<CR>a
nmap <C-S> :w<CR>
" undo
imap <C-Z> <C-[>ua
nmap <C-Z> u
" redo
imap <C-Y> <C-[><C-R>a
nmap <C-Y> <C-R>
" paste
imap <C-P> <C-[>:set paste<CR>a
nmap <C-P> :set nopaste<CR>


imap <CR> <CR>x<BS>
imap {<CR> {<CR>}<UP><END><CR><TAB>

" color column tuning
nmap \| :set colorcolumn=81<CR>
nmap <ESC>\| :set colorcolumn=<CR>
highlight ColorColumn term=reverse ctermbg=DarkGrey

" switch between tabs
imap <ESC>1 <C-[>1gta
nmap <ESC>1 1gt
imap <ESC>2 <C-[>2gta
nmap <ESC>2 2gt
imap <ESC>3 <C-[>3gta
nmap <ESC>3 3gt
imap <ESC>4 <C-[>4gta
nmap <ESC>4 4gt
imap <ESC>5 <C-[>5gta
nmap <ESC>5 5gt
imap <ESC>6 <C-[>6gta
nmap <ESC>6 6gt
imap <ESC>7 <C-[>7gta
nmap <ESC>7 7gt
imap <ESC>8 <C-[>8gta
nmap <ESC>8 8gt
imap <ESC>9 <C-[>9gta
nmap <ESC>9 9gt

