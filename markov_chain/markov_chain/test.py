__author__ = 'josh'

import markov_chain
file_ = open('jeeves.txt')

markov = markov_chain.Markov(file_)
print markov.generate_markov_text().decode('cp949')