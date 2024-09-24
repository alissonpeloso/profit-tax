FROM sail-8.3/app:latest

RUN apt-get update && apt-get install -y python3 python3-pip

RUN pip3 install --upgrade pip

# get the requirements file
COPY brokerage_extractor/requirements.txt /
