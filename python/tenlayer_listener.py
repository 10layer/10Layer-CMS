import time
import sys
import logging
import socket
import stompy
import json

class TenLayerListener:
	def __init__(self):
		logging.basicConfig()
		self.msgcallbacks=[]
	
	def add_callback(self, callback):	
		self.msgcallbacks.append(callback)
		
	def listen(self):
		try:
			stomp=stompy.stomp.Stomp('localhost', 61613)
			stomp.connect()
			stomp.subscribe({'destination':"/action", 'ack':"client"})
			while True:
				frame = stomp.receive_frame()
				msg = json.loads(frame.body)
				func=msg["body"]["func"]
				body=msg["body"]["params"]
				for callback in self.msgcallbacks:
					callback_func=callback["callback"]
					try:
						if (callback["func"]==func):
							callback_func(body)
					except KeyError:
						callback_func(body)
		except socket.error:
			print "Error connecting to Stomp server"