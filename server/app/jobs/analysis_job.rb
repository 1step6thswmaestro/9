class AnalysisJob < ActiveJob::Base
  queue_as :default

  def perform(*args)
    # Do something later
    AnalysisJob.set(wait: 1.day).perform_later
  end
end
