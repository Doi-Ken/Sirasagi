# coding: UTF-8
import os
from os.path import join, dirname
from dotenv import load_dotenv

dotenv_path = join(dirname(__file__), '.env')
load_dotenv(dotenv_path)

FACE_AP= os.environ.get("FACE_API_KEY") # 環境変数の値をAPに代入
EMOTION_AP= os.environ.get("EMOTION_API_KEY") # 環境変数の値をAPに代入

FACE_AP_URL= os.environ.get("FACE_API_URL") # 環境変数の値をAPに代入
EMOTION_AP_URL= os.environ.get("EMOTION_API_URL") # 環境変数の値をAPに代入
