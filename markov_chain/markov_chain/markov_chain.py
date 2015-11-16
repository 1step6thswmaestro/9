# -*- coding:utf-8 -*-
__author__ = 'josh'
import random


class Markov(object):

	def __init__(self, open_file):
		self.cache = {}
		self.cache2 ={}
		self.open_file = open_file
		self.words = self.file_to_words()
		self.word_size = len(self.words)
		self.database()

	def file_to_words(self):
		self.open_file.seek(0)
		data = self.open_file.read()
		words =  data.split()
		return words

	def triples(self):#3단계의 마르코프 체인을 만듬 ( {(단어1),(단어2, 단어3)})
		if len(self.words) < 3:
			return

		for i in range(len(self.words) - 2):
			yield (self.words[i], self.words[i+1], self.words[i+2])

	def double(self):#2단계의 마르코프 체인을 만듬( {(단어1),(단어2,단어3)})
		if len(self.words) < 3:
			return

		for i in range(len(self.words) - 2):
			yield (self.words[i], self.words[i+1], self.words[i+2])

	def database(self):#소설 txt 파일을 단어:단어 map으로 데이터 베이스화
		for w1, w2, w3 in self.triples():
			key = (w1, w2)
			if key in self.cache:
				self.cache[key].append(w3)
			else:
				self.cache[key] = [w3]

		for w1, w2, w3 in self.double():
			key = (w1)
			if key in self.cache:
				self.cache[key].append(w2)
				self.cache2[key].append(w3)
			else:
				self.cache[key] = [w2]
				self.cache2[key] = [w3]

	def generate_markov_text(self, size=300):#size 만큼의 마르코프 체인 문장을 생성
		seed = random.randint(0, self.word_size-3)
		seed_word, next_word = self.words[seed], self.words[seed+1]
		w1, w2 = seed_word, next_word
		gen_words = []
		for i in xrange(size):
			gen_words.append(w1)#마르코프 체인에 w1 단어를 삽입
			if len(self.cache[(w1,w2)]) <= 1: #(w1,w2)에 매핑되는 단어가 1개일 경우
				temp = random.randint(0,len(self.cache[(w1)])-1) #w1에 매핑되는 단어 (w2,w3)무리들 중 랜덤하게 하나를고름
				w1, w2 = self.cache[(w1)][temp], self.cache2[(w1)][temp]#w1에 w2 w2에 w3를 넣어줌
			else:#매핑 되는 단어가 2개 이상일 경우
				w1, w2 = w2, random.choice(self.cache[(w1, w2)])#(w1,w2)에 매핑되는 단어중 랜덤하게 하나를 고르고 w1에 w2를 w2에 랜덤하게 선택된 단어를 넣어줌

			if((i+1)%20==0):
				gen_words.append('\n')

		gen_words.append(w2)
		return ' '.join(gen_words)

