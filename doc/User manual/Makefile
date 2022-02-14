all: en es

clean:
	@find . -name "*.aux" -exec rm {} +
	@find . -name "*.log" -exec rm {} +
	@find . -name "*.out" -exec rm {} +
	@find . -name "*.toc" -exec rm {} +
	@find . -name "*.fls" -exec rm {} +
	@find . -name "*.fdb_latexmk" -exec rm {} +
	@find . -name "*.synctex.gz" -exec rm {} +

en:
	@pdflatex "Neosluger Manual (EN).tex"
	@pdflatex "Neosluger Manual (EN).tex"

es:
	@pdflatex "Neosluger Manual (ES).tex"
	@pdflatex "Neosluger Manual (ES).tex"
