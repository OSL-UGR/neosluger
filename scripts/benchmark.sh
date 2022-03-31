#!/usr/bin/env sh

# Flags {{{1
fabort=0   # An error has been found and the program must abort.
fhelp=0    # Print the help text and exit.
# }}}1


# Globals {{{1
address="localhost"
iterations=1000
# }}}1


# print_quick_help {{{1
# Print a short version of the help text and exit with $fabort.

print_quick_help ()
{
cat << EOF
Usage: benchmark.sh [-h] [[-a ADDR] [-i ITER]]

Operational arguments:
  -h, --help                    Print a complete help text and exit.
  -a ADDR, --address=ADDR       Set Nsl's address.
  -i ITER, --iterations=ITER    Set the number of iterations.
EOF
exit "$fabort"
}
# }}}1


# print_help {{{1
# Print the help text and exit with $fabort.

print_help ()
{
cat << EOF
BENCHMARK
=========
Run a benchmark for Nsl's URL creation API
------------------------------------------------

:: DEPENDENCIES

- Nsl: It must be listening to the API before running the benchmark.
- time: Must be the one present in /usr/bin/time, not the shell builtin command.

:: USAGE

  benchmark.sh [-h] [[-a ADDR] [-i ITER]]

With no arguments the script will run 1000 API calls to localhost. The real time
spent in every call is logged in "benchmark_result". You can customise the
script's behaviour with the following arguments:

  SHORT ARG    LONG ARG            DESCRIPTION
  ----------------------------------------------
  -h           --help              Print this help text and exit.

  -a ADDR     --addres=ADDR        Set Nsl's address to call its API.
                                   Defaults to localhost. Set it without the
                                   last '/' character.

  -i ITER     --iterations=ITER    Set the number of API calls the benchmark
                                   will run and log.

Keep in mind that this script only logs the real time spect in every call in its
original format. You are responsible for how you deal with the data.

:: WHY THE API?

Every time we call the API Nsl generates a new URL and checks if its
handle collides with the ones already in the database. It repeats the process
until there are no collissions and then it inserts the URL in the database, so
we know that each API call does at least one read and one write on the database,
which is what we're benchmarking here.

:: LICENCE

Licenced under the GNU Affero General Public License v3.0:
  https://www.gnu.org/licenses/agpl-3.0.en.html
  https://github.com/OSL-UGR/neosluger

THERE IS NO WARRANTY FOR THE PROGRAM, TO THE EXTENT PERMITTED BY APPLICABLE LAW.
EXCEPT WHEN OTHERWISE STATED IN WRITING THE COPYRIGHT HOLDERS AND/OR OTHER
PARTIES PROVIDE THE PROGRAM "AS IS" WITHOUT WARRANTY OF ANY KIND, EITHER
EXPRESSED OR IMPLIED, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE. THE ENTIRE RISK AS TO THE
QUALITY AND PERFORMANCE OF THE PROGRAM IS WITH YOU. SHOULD THE PROGRAM PROVE
DEFECTIVE, YOU ASSUME THE COST OF ALL NECESSARY SERVICING, REPAIR OR CORRECTION.
EOF
exit "$fabort"
}
# }}}1


# Printf wrappers {{{1
# A small interface to show program status information to the user with printf.

printf_bigarrow ()
{
	printf "\033[32;1m==> \033[0;1m%s\033[0m%s\n" "$1" "$2"
}

printf_error ()
{
	printf "\033[31;1m:: %s!\033[0m%s\n" "$1" "$2"
}

printf_smallarrow ()
{
	printf "\033[35;1m  -> \033[0;1m%s\033[0m%s\n" "$1" "$2"
}
# }}}1


# parse_args {{{1
# Set program flags depending on the arguments passed from the shell.
# Unrecognised arguments are aditionally displayed to the user.

parse_args ()
{
	while [ $# -gt 0 ]
	do
		case "$1"
		in
		-h|--help)
			fhelp=1
		;;
		-a)
			address="$2"
			shift
		;;
		--address=*)
			address="$(echo "$1" | sed 's/^[^=]\+=//g')"
		;;
		-i)
			iterations="$2"
			shift
		;;
		--iterations=*)
			iterations="$(echo "$1" | sed 's/^[^=]\+=//g')"
		;;
		*)
			printf_error "Unrecognised argument: " "$1"
			fabort=1
		;;
		esac

		shift
	done

	if [ $fhelp -eq 1 ]
	then
		print_help
	elif [ $fabort -ne 0 ]
	then
		print_quick_help
	fi
}
# }}}1


# run_benchmark {{{1
# Main loop of the program. Calls Nsl's API $iterations times and logs the
# real time spent processing the call in "benchmark_result".

run_benchmark ()
{
	printf_bigarrow "Starting benchmark for $iterations iterations"

	i=0
	while [ $i -lt "$iterations" ]
	do
		printf_smallarrow "Running iteration $i/$iterations"

		result="$(/usr/bin/time curl "$address"/api?url=http://e.e 2>&1)"
		realtime="$(\
			echo "$result" \
			| grep "elapsed" \
			| sed 's/.*\([0-9]:[0-9]\{2\}\.[0-9]\{2\}\)elapsed.*/\1/g' \
		)"

		echo "$i $realtime" >> benchmark_result
		: $((i += 1))
	done

	printf_bigarrow "Benchmark complete!"
}
# }}}1


main ()
{
	parse_args "$@"
	run_benchmark
}

main "$@"
