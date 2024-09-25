FROM sail-8.3/app:latest

COPY brokerage_extractor/requirements.txt /app/requirements.txt

RUN apt-get update && apt-get install -y \
    python3-pip
RUN pip3 install -r /app/requirements.txt
